import {router} from "@inertiajs/vue3";
import {Toast, toaster} from "./generic-helpers";
import CryptoJS from "crypto-js";
import elliptic from "elliptic";
import {mnemonicToSeed} from "bip39";

const ecdh_25519 = new elliptic.ec('curve25519');         // ECDH for curve25519 - For key exchange
const eddsa_ed25519 = new elliptic.eddsa('ed25519');   // EdDSA for Ed25519 - For signing

async function getSeed(mnemonicPhrase) {
    return (await mnemonicToSeed(mnemonicPhrase)).toString();
}

async function getEllipticKeyPairFromMnemonic(mnemonicPhrase, mode) {
    const seed = await getSeed(mnemonicPhrase);
    return mode.toLowerCase() === "ecdh" ? ecdh_25519.keyFromPrivate(seed) : eddsa_ed25519.keyFromSecret(seed);
}

export async function validatePrivateKey(mnemonicPhrase, publicKeys) {
    const senderKeyEcdh = await getEllipticKeyPairFromMnemonic(mnemonicPhrase, "ecdh");
    const senderKeyEddsa = await getEllipticKeyPairFromMnemonic(mnemonicPhrase, "eddsa");
    const senderPublKeyEcdh = senderKeyEcdh.getPublic('hex');
    const senderPublKeyEddsa = senderKeyEddsa.getPublic('hex');
    return senderPublKeyEcdh === publicKeys[0] && senderPublKeyEddsa === publicKeys[1];
}

function getSharedKey(keyPair, receiverPubKey) { // Used only for ECDH
    return keyPair.derive(ecdh_25519.keyFromPublic(receiverPubKey, 'hex').getPublic()).toString(16);
}

export async function getSharedKeyFromMnemonic(PrvMnemonic, receiverPubKey) {
    const senderKeyPair = await getEllipticKeyPairFromMnemonic(PrvMnemonic, "ecdh");
    return getSharedKey(senderKeyPair, receiverPubKey);
}

async function signMessage(PrvMnemonic, message) {
    // const wordArray = CryptoJS.lib.WordArray.create(message);
    const key = eddsa_ed25519.keyFromSecret(await getSeed(PrvMnemonic));
    return key.sign(message).toHex(); // signature
}

// receiverPubKey is an array of two public keys [ECDH, EdDSA]
async function safeMessage(content, senderPrvMnemonic, receiverPubKey) {
    const signature = await signMessage(senderPrvMnemonic, content);
    const signedContent = JSON.stringify({content, signature});
    const sharedKey = await getSharedKeyFromMnemonic(senderPrvMnemonic, receiverPubKey[0]);
    return {
        encryptedSignedContent: CryptoJS.AES.encrypt(signedContent, sharedKey).toString(),
        signature: signature
    };
}

// receiverPubKey is an array of two public keys [ECDH, EdDSA]
export async function sendMessage(content, conversationId, senderPrvMnemonic, receiverPubKey) {
    const {encryptedSignedContent, signature} = await safeMessage(content, senderPrvMnemonic, receiverPubKey);
    return new Promise((resolve, reject) => {
        router.post(route('messages.store'), {
            conversationId: conversationId,
            content: encryptedSignedContent,
            signature: signature
        }, {
            preserveScroll: true,
            onError: (e) => {
                Toast.fire({
                    icon: 'error',
                    title: Object.values(e)
                });
                reject(e);
            },
            onSuccess: () => {
                resolve();
            },
        });
    });
}

function decMsg(content, sharedKey) {
    return CryptoJS.AES.decrypt(content, sharedKey);
}

export function validateMsg(content, signature, targetEddsaPubKey) {
    const senderPubKey = eddsa_ed25519.keyFromPublic(targetEddsaPubKey, 'hex')
    return senderPubKey.verify(content, signature);
}

export async function decryptMessage(message, prvMnemonic, targetPubKeys, srcPubKeys = null, keyValidation = false, realtime= false, authPubKeys = null) {
    return new Promise(async (resolve, reject) => {
        try {
            if (!keyValidation) {
                const isValidKey = await validatePrivateKey(prvMnemonic, realtime ? targetPubKeys : srcPubKeys);
                if (!isValidKey) {
                    message.encrypted = true;
                    reject('Empty/Incorrect Secret Key... Decrypting failed');
                    return;
                }
            }
            const sharedKey = await getSharedKeyFromMnemonic(prvMnemonic, realtime ? srcPubKeys[0] : (authPubKeys[0] === targetPubKeys[0] ? targetPubKeys[0] : srcPubKeys[0]));
            const decryptedMessage = decMsg(message.content, sharedKey).toString(CryptoJS.enc.Latin1);
            const { content, signature } = JSON.parse(decryptedMessage);
            const isValid = validateMsg(content, signature, srcPubKeys[1]);
            if (!isValid) {
                message.invalid = true;
                console.log(content)
                toaster('error', 'Message is not valid');
                reject('Message is not valid');
                return;
            }
            message.content = content;
            resolve(content);
        } catch (e) {
            reject(e);
        }
    });
}


// targetPubKey is an array of two public keys [ECDH, EdDSA]
export async function decryptConversation(conversation, prvMnemonic, authPubKeys = null) {
    return new Promise(async (resolve, reject) => {
        conversation.processing = true;
        let isValidKey = false;

        if (!prvMnemonic){
            conversation.processing = false;
            conversation.messages.forEach(message => message.encrypted = true);
            reject('Empty Secret Key... Conversation Decrypting Halted');
            return
        }

        if (authPubKeys) {
            isValidKey = await validatePrivateKey(prvMnemonic, authPubKeys);
        }
        for (const message of conversation.messages) {
            // Skeletons
            conversation.processing = true;
            message.encrypted = true;

            // Validation
            if (message.content === '') {
                conversation.processing = false;
                continue
            }
            if (!message.content.startsWith('U2FsdGVkX1') && isValidKey) {
                message.encrypted = false;
                conversation.processing = false;
                continue;
            }

            // Decryption
            try {
                let senderKeys = null;
                let receiverKeys = null;
                if (message.sender_id === conversation.user_1.id) {
                    senderKeys = [conversation.user_1.public_key_ecdh, conversation.user_1.public_key_eddsa];
                    receiverKeys = [conversation.user_2.public_key_ecdh, conversation.user_2.public_key_eddsa];
                } else {
                    senderKeys = [conversation.user_2.public_key_ecdh, conversation.user_2.public_key_eddsa];
                    receiverKeys = [conversation.user_1.public_key_ecdh, conversation.user_1.public_key_eddsa];
                }
                await decryptMessage(message, prvMnemonic, receiverKeys, senderKeys, true, false, authPubKeys);
                message.encrypted = false;
                message.invalid = true;
                resolve();
            } catch (e) {
                reject(e);
            }
            finally {
                conversation.processing = false;
            }
        }
    });

}

export function deleteMessage(id) {
    return new Promise((resolve, reject) => {

        router.delete(route('messages.destroy', {id: id}), {
            preserveScroll: true,
            onError: (e) => {
                Toast.fire({
                    icon: 'error',
                    title: Object.values(e)
                });
                reject(e);
            },
            onSuccess: () => {
                Toast.fire({
                    icon: "success",
                    title: "Message deleted successfully!"
                });
                resolve(1);
            },
        });
        //
        // Swal.fire({
        //     title: 'Are you sure?',
        //     text: "The message will be deleted for both you and the recipient!",
        //     icon: 'warning',
        //     showCancelButton: true,
        //     confirmButtonColor: '#d33',
        //     cancelButtonColor: '#3085d6',
        //     confirmButtonText: 'Yes, delete it!'
        // }).then((result) => {
        //     if (result.isConfirmed) {
        //         router.delete(route('messages.destroy', {id: id}), {
        //             preserveScroll: true,
        //             onError: (e) => {
        //                 Toast.fire({
        //                     icon: 'error',
        //                     title: Object.values(e)
        //                 });
        //                 reject(e);
        //             },
        //             onSuccess: () => {
        //                 Toast.fire({
        //                     icon: "success",
        //                     title: "Message deleted successfully!"
        //                 });
        //                 resolve(1);
        //             },
        //         });
        //     }
        // });
    });
}
