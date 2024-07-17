import { Toast } from "./generic-helpers";
import Swal from "sweetalert2";
import {router} from "@inertiajs/vue3";

// Review the 'async' functions here. Probably they should be 'sync' functions?

export async function removeContact(id) {
    Swal.fire({
        title: "Are you sure you want to delete this cotnact?",
        text: "All your conversations with this contact will be deleted as well.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, Delete!",
        denyButtonText: "No, Cancel!"
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('contacts.destroy', {id: id}), {
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
                        title: "Contact deleted successfully!"
                    });
                },
            });
        }
    });
}

// selectedContacts is an array of selected IDs
export async function sendContactRequest(selectedContacts){
    router.post(route('contacts.store'), {selectedContacts: selectedContacts}, {
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
                title: "Request sent successfully!"
            });
        },
    });
}

export async function cancelContactRequest(id){
    router.delete(route('sent-request-destroy', {id: id}), {
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
                title: "Request removed successfully!"
            });
        },
    });
}
export async function contactRequestResponse(id, choice) {
    router.patch(route('contacts.update', {id: id}), {choice: choice} , {
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
                title: "Operation Successful!"
            });
        },
    });

}

