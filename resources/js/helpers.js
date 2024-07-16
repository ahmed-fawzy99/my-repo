import CryptoJS from "crypto-js";
import {mnemonicToSeed} from "bip39";
import elliptic from "elliptic";
import Swal from "sweetalert2";

export const Toast = Swal.mixin({
    toast: true,
    position: "top-right",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});
export function downloadFile(blob, filename='file') {
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
export function truncate(str, n= 32){
    return (str.length > n) ? str.slice(0, n-1) + '...' : str;
};
export function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
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

export async function fancyPrompt(title, inputPlaceholder = '', inputType = 'text', showCancelButton = true) {
    const { value: result } = await Swal.fire({
        title: title,
        input: inputType,
        inputPlaceholder: inputPlaceholder,
        showCancelButton: showCancelButton
    });
    if (!result) {
        Toast.fire({
            icon: 'info',
            title: 'Operation Cancelled.'
        });
        return false;
    }
    return result;
}
