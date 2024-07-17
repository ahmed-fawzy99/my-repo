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



export async function fancyPrompt(title, inputPlaceholder = '', inputType = 'text', showCancelButton = true) {
    const { value: result } = await Swal.fire({
        title: title,
        input: inputType,
        inputPlaceholder: inputPlaceholder,
        showCancelButton: showCancelButton
    });
    if (!result) {
        return Toast.fire({
            icon: 'info',
            title: 'Operation Cancelled.'
        });
    }
    return result;
}
