import CryptoJS from "crypto-js";
import {mnemonicToSeed} from "bip39";
import elliptic from "elliptic";
import {downloadFile, fancyPrompt, Toast} from "@/js-helpers/generic-helpers.js";
import Swal from "sweetalert2";
import {router} from "@inertiajs/vue3";


export async function sendFile(contactId){

    // To be implemented later

    // console.log(contactId)
    // const { value: file } = await Swal.fire({
    //     title: "Select File",
    //     input: "file",
    //
    // });
    // if (file) {
    // }
}

export async function uploadDecrypted(fileForm, usePrivateKey, encryptionKey){
    const reader = new FileReader();
    reader.onload = async (e) => {

        let key;
        if (usePrivateKey.value){
            const mnemonicSeed = await fancyPrompt('Enter your Secret Phrase:', 'Example: sunrise table mountain tourist carbon fire crystal dragon artwork daemon pistol broccoli', "textarea", undefined)
            if (!mnemonicSeed)
                return;
            key = generateKey();
            const privateKey = await getPrivateKey(mnemonicSeed);
            fileForm.key = CryptoJS.AES.encrypt(key, privateKey).toString();

        } else {
            key = encryptionKey.value;
        }

        const wordArray = CryptoJS.lib.WordArray.create(e.target.result);
        fileForm.choice = usePrivateKey.value;


        fileForm.file = new Blob([CryptoJS.AES.encrypt(wordArray, key).toString()]);

        fileForm.checksum = CryptoJS.MD5(wordArray.toString()).toString() // MD5Sum

        fileForm.post(route('store-file'), {
            preserveScroll: true,
            onError: (e) => {
                Toast.fire({
                    icon: 'error',
                    title: Object.values(e)
                });
            },
            onSuccess: () => {
                document.getElementById('file-input').value = '';
                fileForm.reset();
                Toast.fire({
                    icon: "success",
                    title: "File uploaded successfully!"
                });
            },
        });
    };
    reader.readAsArrayBuffer(fileForm.file);
}
export async function downloadDecrypted(uuid, file_name, enc_key, checksum, routeName= 'get-file', passedKey = null){
    try {
        const response = await fetch(route(routeName, {uuid: uuid}));
        const encryptedFile = await response.text();
        let fileKey;
        if (enc_key){
            const mnemonicSeed = await fancyPrompt('Enter your Secret Phrase:', 'Example: sunrise table mountain tourist carbon fire crystal dragon artwork daemon pistol broccoli', "textarea")
            if (!mnemonicSeed)
                return;
            const privateKey = await getPrivateKey(mnemonicSeed);
            fileKey = CryptoJS.AES.decrypt(enc_key, privateKey).toString(CryptoJS.enc.Latin1);
        } else if (!enc_key && passedKey && routeName === 'download-shared'){ // if we're downloading a shared file, key is already passed
            fileKey = passedKey;
        }
        else {
            fileKey = await fancyPrompt('Enter the Encryption Key:', 'Ca1i¢0CatsRTheBe$tBa8ie$')
            if (!fileKey)
                return;
        }
        const decrypted = CryptoJS.AES.decrypt(encryptedFile, fileKey);
        const decryptedChecksum = CryptoJS.MD5(decrypted.toString()).toString();
        if (decryptedChecksum !== checksum) {
            Toast.fire({
                icon: 'error',
                title: 'Checksums do not match. Your key is probably wrong.'
            });
            return;
        }
        const blob = new Blob([convertWordArrayToUint8Array(decrypted)],
            { type: response.headers.get('Content-Type') });
        downloadFile(blob, file_name);

        Toast.fire({
            icon: 'success',
            title: 'Downloaded & Decrypted successfully ✅\nChecksum Correct! ✅'
        });
    } catch (e) {
        Toast.fire({
            icon: 'error',
            title: e
        });
    }
}

export async function share(uuid){
    try {
        const response = await fetch(route('share-file', {uuid: uuid}));
        const data = await response.json();
        Swal.fire({
            icon: "info",
            title: "SharableFile Link",
            html: `
            Here is the link to share the file: <br><br>
            <a href="${route().t.url}/get-shared-file/${data.file_uuid}" target="_blank" class="text-primary-600 underline">
                ${route().t.url}/get-shared-file/${data.file_uuid}
            </a>`,
            showCancelButton: true,
            cancelButtonText: "Close",
            confirmButtonText: "Copy Link",
            preConfirm: async (login) => {
                await navigator.clipboard.writeText(`${route().t.url}/get-shared-file/${data.file_uuid}`);
                Toast.fire({
                    icon: 'success',
                    title: 'Link copied to clipboard!'
                });
            }
            });
    } catch (e) {
        Toast.fire({
            icon: 'error',
            title: e
        });
    }
}

export async function renameFile(uuid, oldFileName) {
    const { value: newFileName } = await Swal.fire({
        title: `Renaming File ${oldFileName}`,
        input: "text",
        inputLabel: "Enter the new file name",
        inputValue: oldFileName
    });
    if (newFileName) {
        if (newFileName === oldFileName) {
            Toast.fire({
                icon: 'info',
                title: 'The new file name is the same as the old file name. No changes made.'
            });
            return;
        }
        router.patch(route('rename-file', {uuid, newFileName}), {
            preserveScroll: true,
            onError: (e) => {
                Toast.fire({
                    icon: 'error',
                    title: Object.values(e)
                });
            },
            onSuccess: () => {
                Toast.fire({
                    icon: "success",
                    title: "File Renamed successfully!"
                });
            },
        });
    } else {
        Toast.fire({
            icon: 'info',
            title: 'Empty Name entered. No changes made.'
        });
    }
}

export async function removeFile(uuid) {
    Swal.fire({
        title: "Are you sure you want to delete this file?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, Delete!",
        denyButtonText: "No, Cancel!"
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('delete-file', {uuid: uuid}), {
                preserveScroll: true,
                onError: (e) => {
                    Toast.fire({
                        icon: 'error',
                        title: Object.values(e)
                    });
                },
                onSuccess: () => {
                    Toast.fire({
                        icon: "success",
                        title: "File deleted successfully!"
                    });
                },
            });
        }
    });
}
export function convertWordArrayToUint8Array(wordArray) {
    var arrayOfWords = wordArray.hasOwnProperty("words") ? wordArray.words : [];
    var length = wordArray.hasOwnProperty("sigBytes") ? wordArray.sigBytes : arrayOfWords.length * 4;
    var uInt8Array = new Uint8Array(length), index=0, word, i;
    for (i=0; i<length; i++) {
        word = arrayOfWords[i];
        uInt8Array[index++] = word >> 24;
        uInt8Array[index++] = (word >> 16) & 0xff;
        uInt8Array[index++] = (word >> 8) & 0xff;
        uInt8Array[index++] = word & 0xff;
    }
    return uInt8Array;
}

export function generateKey (n = 32) {
    return CryptoJS.lib.WordArray.random(n / 2).toString();
}

export async function getPrivateKey(mnemonicSecret) {
    const seed = (await mnemonicToSeed(mnemonicSecret)).toString('hex')
    return (new elliptic.ec("curve25519")).keyFromPrivate(seed).getPrivate('hex');
}
