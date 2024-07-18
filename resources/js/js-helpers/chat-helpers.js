import {router} from "@inertiajs/vue3";
import {Toast, toaster} from "./generic-helpers";
import Swal from "sweetalert2";
import CryptoJS from "crypto-js";
import elliptic from "elliptic";
import {mnemonicToSeed} from "bip39";

const ec_25519 = new elliptic.ec('curve25519');         // ECDH for curve25519 - For key exchange
const ec_ed25519 = new elliptic.eddsa('ed25519');   // EdDSA for Ed25519 - For signing

async function getSeed(mnemonicPhrase) {
    return (await mnemonicToSeed(mnemonicPhrase)).toString('hex');
}
async function getEllipticKeyPairFromMnemonic(mnemonicPhrase) {
    const seed = await getSeed(mnemonicPhrase);
    return ec_25519.keyFromPrivate(seed);
}
export async function validatePrivateKey(mnemonicPhrase, publicKey) {
    const senderKey =  await getEllipticKeyPairFromMnemonic(mnemonicPhrase) ;
    const senderPublKey = senderKey.getPublic('hex');
    return senderPublKey === publicKey;
}

function getSharedKey(keyPair, receiverPubKey) {
    return keyPair.derive(ec_25519.keyFromPublic(receiverPubKey, 'hex').getPublic()).toString(16);
}
export async function getSharedKeyFromMnemonic(PrvMnemonic, receiverPubKey) {
    const senderKeyPair = await getEllipticKeyPairFromMnemonic(PrvMnemonic);
    return getSharedKey(senderKeyPair, receiverPubKey);
}
function signMessage(PrvMnemonic, message) {
    const wordArray = CryptoJS.lib.WordArray.create(message);
    const key = ec_ed25519.keyFromSecret(getSeed(PrvMnemonic));
    return key.sign(wordArray).toHex(); // signature
}

async function safeMessage(content, senderPrvMnemonic, receiverPubKey) {
    const signature = signMessage(senderPrvMnemonic, content);
    const signedContent = JSON.stringify({content, signature});
    const sharedKey = await getSharedKeyFromMnemonic(senderPrvMnemonic, receiverPubKey);
    return {
        encryptedSignedContent: CryptoJS.AES.encrypt(signedContent, sharedKey).toString(),
        signature: signature
    };
}

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

export async function decryptConversation(conversation, prvMnemonic, targetPubKey) {
    const sharedKey = await getSharedKeyFromMnemonic(prvMnemonic, targetPubKey);
    for (const message of conversation.messages) {
        const decryptedMessage = CryptoJS.AES.decrypt(message.content, sharedKey).toString(CryptoJS.enc.Latin1);
        const {content, signature} = JSON.parse(decryptedMessage);

        const senderPubKeyStr = conversation.user_1.id === message.sender_id ? conversation.user_1.public_key : conversation.user_2.public_key;

        const kk = ec_ed25519.keyFromSecret(getSeed(prvMnemonic)).getPublic('hex');

        const senderPubKey = ec_ed25519.keyFromPublic(senderPubKeyStr, 'hex')
        console.log(kk);
        const isValid = senderPubKey.verify(CryptoJS.lib.WordArray.create(content), signature);
        console.log(isValid, senderPubKeyStr);

        if (!isValid) {
            toaster('error', 'Message is not valid');
            return false;
        }
        message.content = content;
    }
}
export function deleteMessage(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "The message will be deleted for both you and the recipient!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('messages.destroy', {id: id}), {
                preserveScroll: true,
                preserveState: true,
                onError: (e) => {
                    Toast.fire({
                        icon: 'error',
                        title: Object.values(e)
                    });
                },
                onSuccess: () => {
                    Toast.fire({
                        icon: "success",
                        title: "Message deleted successfully!"
                    });
                },
            });
        }
    });
}
