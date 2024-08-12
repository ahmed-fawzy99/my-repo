<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {formatFileSize} from "@/js-helpers/generic-helpers.js";
import ShareLayout from "@/Layouts/ShareLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import {ref} from "vue";
import {downloadDecrypted} from "@/js-helpers/crypto-helpers.js";

const props = defineProps({
    file: Object,
    owner: String,
});
const decKey = ref('');

const download = () => {
    if (decKey.value){
        downloadDecrypted(props.file.uuid, props.file.file_name, props.file.custom_properties.enc_key, props.file.custom_properties.checksum, 'download-shared', decKey.value)
    } else {
        alert('Please enter decryption key')
    }
}

</script>

<template>
    <ShareLayout>
        <div class="mb-4 text-center text-base-900 dark:text-base-100">
            <h1 class="text-3xl font-semibold mb-2">Download File</h1>
            <table class="w-full mx-auto mt-8">
                <tr class="flex justify-between border-b py-2">
                    <td class="font-bold w-1/4 text-start">File Owner:</td>
                    <td class="text-end break-all">{{ owner }}</td>
                </tr>
                <tr class="flex justify-between border-b py-2">
                    <td class="font-bold w-1/4 text-start">File Name:</td>
                    <td class="text-end break-all">{{ file.name }}</td>
                </tr>
                <tr class="flex justify-between border-b py-2">
                    <td class="font-bold w-1/4 text-start">File Size:</td>
                    <td class="text-end">{{ formatFileSize(file.size) }}</td>
                </tr>
                <tr class="flex justify-between border-b items-center py-2">
                    <td class="font-bold w-1/4 text-start my-auto">Decryption Key:</td>
                    <td class="w-3/4">
                        <TextInput class="w-full" v-model="decKey"/>
                    </td>
                </tr>
            </table>
        </div>

        <div class="flex items-center justify-center mt-4">
            <PrimaryButton class="ms-4" @click="download">
                Download
            </PrimaryButton>
        </div>

    </ShareLayout>
</template>

<style scoped>

</style>
