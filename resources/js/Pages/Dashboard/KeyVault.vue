<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import Card from "@/Components/Card.vue";

import PrimaryButton from "@/Components/PrimaryButton.vue";
import {getPrivateKey, truncate} from "@/helpers.js";
import TableBodyHeader from "@/Components/Table/TableBodyHeader.vue";
import Table from "@/Components/Table/Table.vue";
import TableHead from "@/Components/Table/TableHead.vue";
import TableRow from "@/Components/Table/TableRow.vue";
import TableBody from "@/Components/Table/TableBody.vue";
import CryptoJS from "crypto-js";
import {ref} from "vue";

const props = defineProps({
    files: Object,
});

const isRevealed = ref(false);
const decrypted = ref(false);

const fillTable = (text = undefined) => {
    props.files.data.forEach((file) => {
        document.getElementById(file.id).innerText = text ?? file.custom_properties.enc_key;
    });
}
async function toggleKeys() {
    if (isRevealed.value){
        fillTable("*******************************");
        isRevealed.value = false;
        return;
    }
    if (!decrypted.value){
        const mnemonicSeed = "sunrise table mountain tourist carbon fire crystal dragon artwork daemon pistol broccoli";
        // const mnemonicSeed = prompt('Enter your secret');
        const privateKey = await getPrivateKey(mnemonicSeed);

        props.files.data.forEach((file) => {
            file.custom_properties.enc_key =
                file.custom_properties.enc_key
                    ? CryptoJS.AES.decrypt(file.custom_properties.enc_key, privateKey).toString(CryptoJS.enc.Utf8)
                    : "User-defined Key";
        });
        decrypted.value = true;
    }
    fillTable()
    isRevealed.value = true;
}

</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-4xl mb-4">Your Key Vault</h1>
                <span class="text-xs">You don't need to worry about this part, MyRepo handles it for you. This page exists for transparency</span>
            </div>
            <div class="flex inline-flex gap-4">
                <PrimaryButton @click="toggleKeys()">
                    <span class="pi pi-eye text-xl mr-2" /> Show You Keys
                </PrimaryButton>
            </div>
        </div>

        <Card class="">
            <Table :links="files.links" :showingNumber="files.data.length" :totalNumber="files.total">
                <template #Head>
                    <TableHead sortable>File Name</TableHead>
                    <TableHead>UUID</TableHead>
                    <TableHead>Key</TableHead>
                </template>
                <template #Body>
                    <TableRow v-for="file in files.data" :key="file.id">
                        <TableBodyHeader :href="'#'">{{ file.file_name }}</TableBodyHeader>
                        <TableBody :href="'#'">{{ file.uuid }}</TableBody>
                        <TableBody :href="'#'" :id="file.id">*******************************</TableBody>
                    </TableRow>
                </template>
            </Table>
        </Card>
    </AuthenticatedLayout>
</template>
