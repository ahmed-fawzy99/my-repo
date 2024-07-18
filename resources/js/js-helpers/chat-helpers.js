import {router} from "@inertiajs/vue3";
import {Toast, toaster} from "./generic-helpers";
import Swal from "sweetalert2";
import CryptoJS from "crypto-js";
import elliptic from "elliptic";
import {mnemonicToSeed} from "bip39";

const ecdh_25519 = new elliptic.ec('curve25519');         // ECDH for curve25519 - For key exchange
const eddsa_ed25519 = new elliptic.eddsa('ed25519');   // EdDSA for Ed25519 - For signing

async function getSeed(mnemonicPhrase) {
    return (await mnemonicToSeed(mnemonicPhrase)).toString('hex');
}
async function getEllipticKeyPairFromMnemonic(mnemonicPhrase, mode) {
    const seed = await getSeed(mnemonicPhrase);
    return mode.toLowerCase() === "ecdh" ? ecdh_25519.keyFromPrivate(seed) : eddsa_ed25519.keyFromSecret(seed);
}
export async function validatePrivateKey(mnemonicPhrase, publicKeys) {
    const senderKeyEcdh =  await getEllipticKeyPairFromMnemonic(mnemonicPhrase, "ecdh");
    const senderKeyEddsa =  await getEllipticKeyPairFromMnemonic(mnemonicPhrase, "eddsa");

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
    const {encryptedSignedContent, signature} =  await safeMessage(content, senderPrvMnemonic, receiverPubKey);
    return new Promise((resolve, reject) => {
        router.post(route('messages.store'), {conversationId: conversationId, content: encryptedSignedContent, signature: signature},{
            preserveScroll: true,
            onError: (e) => {
                Toast.fire({
                    icon: 'error',
                    title: Object.values(e)
                });
                reject(e);
            },
            onSuccess: () => {
                resolve(1);
            },
        });
    });
}

// targetPubKey is an array of two public keys [ECDH, EdDSA]
export async function decryptConversation(conversation, prvMnemonic, targetPubKeys) {
    const sharedKey = await getSharedKeyFromMnemonic(prvMnemonic, targetPubKeys[0]);
    for (const message of conversation.messages) {
        if (message.content === '') continue;
        const decryptedMessage = CryptoJS.AES.decrypt(message.content, sharedKey).toString(CryptoJS.enc.Latin1);
        const {content, signature} = JSON.parse(decryptedMessage);

        const senderPubKeyStr = conversation.user_1.id === message.sender_id ?
            conversation.user_1.public_key_eddsa : conversation.user_2.public_key_eddsa;

        const senderPubKey = eddsa_ed25519.keyFromPublic(senderPubKeyStr, 'hex')
        const isValid = senderPubKey.verify(content, signature);
        message.content = content;

        if (!isValid) {
            toaster('error', 'Message is not valid');
            message.content = '[INVALID CONTENT] ' + message.content;
            return false;
        }
        message.content = content;
    }
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
