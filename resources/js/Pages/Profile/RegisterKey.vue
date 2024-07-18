<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import Checkbox from '@/Components/Checkbox.vue';
import {Head, router} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import elliptic from "elliptic";
import {generateMnemonic, mnemonicToSeed} from "bip39";
import Swal from "sweetalert2";
import {copyToClipboard} from "@/js-helpers/generic-helpers.js";

const Toast = Swal.mixin({
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
const mnemonic = ref('')
const publicKey = ref('')
const keyConfirmation = ref(false)
const processing = ref(true);

async function generateKey() {
    mnemonic.value = generateMnemonic(128);
    const seed = (await mnemonicToSeed(mnemonic.value)).toString('hex')
    const ec = new elliptic.ec("curve25519");
    const keyPair = ec.keyFromPrivate(seed);
    publicKey.value = keyPair.getPublic('hex');
    processing.value = false;
}

onMounted(() => {
    generateKey();
})
</script>

<template>
    <AuthLayout>
        <Head title="Register" />

        <div class="mb-4 text-center text-base-900 dark:text-base-100">
            <h1 class="text-3xl font-semibold mb-2">Your Secret Phrase</h1>
            <p> This is your 12 words secret key.
                Write it down and keep it safe as it is the only way to recover your encrypted files.</p>
        </div>

        <div class="flex flex-col p-4 bg-base-100 dark:bg-base-900 rounded-xl text-2xl italic text-justify shadow-sm">
            <svg @click="copyToClipboard(mnemonic)" class="w-3.5 h-3.5 text-base-600 hover:text-base-800 dark:text-base-600
                    dark:hover:text-base-400 cursor-pointer ml-auto"
                 aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                <path d="M16 1h-3.278A1.992 1.992 0 0 0 11 0H7a1.993 1.993 0 0 0-1.722 1H2a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2Zm-3 14H5a1 1 0 0 1 0-2h8a1 1 0 0 1 0 2Zm0-4H5a1 1 0 0 1 0-2h8a1 1 0 1 1 0 2Zm0-5H5a1 1 0 0 1 0-2h2V2h4v2h2a1 1 0 1 1 0 2Z"/>
            </svg>
            <div v-if="processing" role="status" class="max-w-sm animate-pulse py-2">
                <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-700 w-48 mb-4"></div>
                <div class="h-2 bg-gray-300 rounded-full dark:bg-gray-700 max-w-[360px] mb-2.5"></div>
                <div class="h-2 bg-gray-300 rounded-full dark:bg-gray-700 mb-2.5"></div>
                <span class="sr-only">Loading...</span>
            </div>
            <p v-else class="py-2">{{ mnemonic }}</p>
        </div>



        <div class="flex items-center justify-end mt-4">
            <div class=" mr-auto align-top">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="keyConfirmation" />
                    <span class="ms-2 text-sm text-base-600  dark:text-base-100">I have written down my secret key.</span>
                </label>
            </div>

            <PrimaryButton @click="router.get(route('finalize-reg', {publicKey}))" class="ms-4"
                           :disabled="!keyConfirmation" :class="{ 'opacity-25 cursor-not-allowed': !keyConfirmation }">
                Log in
            </PrimaryButton>
        </div>

    </AuthLayout>
</template>
