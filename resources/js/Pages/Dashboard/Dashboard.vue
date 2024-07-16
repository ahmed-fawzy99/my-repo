<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {onMounted, ref, watch} from "vue";
import {Head, router, useForm} from '@inertiajs/vue3';
import CryptoJS from 'crypto-js';
import Swal from 'sweetalert2'
import elliptic from "elliptic";
import debounce from "lodash/debounce";

import {
    formatFileSize,
    downloadFile,
    convertWordArrayToUint8Array,
    generateKey,
    getPrivateKey,
    fancyPrompt,
    Toast,
} from "@/helpers.js";

import Table from "@/Components/Table/Table.vue";
import TableRow from "@/Components/Table/TableRow.vue";
import TableBodyHeader from "@/Components/Table/TableBodyHeader.vue";
import TableBody from "@/Components/Table/TableBody.vue";
import TableBodyAction from "@/Components/Table/TableBodyAction.vue";
import TableHead from "@/Components/Table/TableHead.vue";
import Toggle from "@/Components/Toggle.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import FileInput from "@/Components/FileInput.vue";
import Card from "@/Components/Card.vue";
import ToolTip from "@/Components/ToolTip.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Badge from "@/Components/Badge.vue";

const file = ref(null);
const usePrivateKey = ref(true);
const encryptionKey = ref('');
const ec = new elliptic.ec("curve25519");
const sort = ref('file_name');
const sort_dir = ref(true);
const search = debounce(() => {
    router.visit(route('dashboard', {sort: sort.value, sort_dir: sort_dir.value}),
        {preserveState: true, preserveScroll: true})
}, 100);
watch(sort, search);
watch(sort_dir, search);

const fileForm = useForm({file: null, checksum: '', name: '', type: '', key: '', choice: true});
const props = defineProps({
    userFiles: Object,
    filesCount: Number,
    storageUsage: Number,
    quota: Number,
});

const submitFile = () => {
    const reader = new FileReader();
    reader.onload = async (e) => {
        let key;
        if (usePrivateKey.value){
            const mnemonicSeed = "sunrise table mountain tourist carbon fire crystal dragon artwork daemon pistol broccoli" || await fancyPrompt('Enter your Secret Phrase:', 'Example: sunrise table mountain tourist carbon fire crystal dragon artwork daemon pistol broccoli', "textarea", undefined)
            if (!mnemonicSeed)
                return;
            key = generateKey();
            const privateKey = await getPrivateKey(mnemonicSeed);
            fileForm.key = CryptoJS.AES.encrypt(key, privateKey).toString();
        } else {
            key = encryptionKey.value;
        }
        const wordArray = CryptoJS.lib.WordArray.create(e.target.result);
        fileForm.name = file.value.name;
        fileForm.type = file.value.type;
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
    reader.readAsArrayBuffer(file.value);
};

onMounted(() => {
    document.getElementById('file-input').addEventListener('change', (e) => {
        file.value = e.target.files[0];
    });
});

async function download(uuid, file_name, enc_key, checksum) {
    try {
        const response = await fetch(route('get-file', {uuid: uuid}));
        const encryptedFile = await response.text();
        let fileKey;
        if (enc_key){
            const mnemonicSeed = "sunrise table mountain tourist carbon fire crystal dragon artwork daemon pistol broccoli" || await fancyPrompt('Enter your Secret Phrase:', 'Example: sunrise table mountain tourist carbon fire crystal dragon artwork daemon pistol broccoli', "textarea")
            if (!mnemonicSeed)
                return;
            const privateKey = await getPrivateKey(mnemonicSeed);
            fileKey = CryptoJS.AES.decrypt(enc_key, privateKey).toString(CryptoJS.enc.Utf8);
        } else {
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
            title: Object.values(e)
        });
    }
}

async function renameFile(uuid, oldFileName) {
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
function deleteFile(uuid) {
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
</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <h1 class="text-4xl mb-4">Dashboard</h1>
        <div class="flex flex-row gap-4 h-fit">
            <Card class="w-3/4 overflow-visible">
<!--                <span class="relative text-2xl pi pi-plus bg-indigo-600 p-2 rounded-full  shadow-md inline cursor-pointer" />-->
                <h2 class="text-xl">Add File</h2>

                <form @submit.prevent="submitFile" class="pt-4">

                    <FileInput no-label v-model="file"/>
                    <Toggle class="mt-4" v-model="usePrivateKey" label="Encrypt Using My Private Key (Disable If Sharing)"/>
                    <div v-if="!usePrivateKey">
                        <InputLabel for="enc-choice" value="Specific Encryption Key" class="inline"/>
                        <span class="pi pi-refresh ml-2 cursor-pointer text-xs" title="Generate Random Key"
                              @click="encryptionKey = generateKey(16)"/>
                        <ToolTip direction="right">This is the key that will be used to decrypt the file. Don't lose
                            it!
                        </ToolTip>
                        <TextInput
                            id="enc-choice"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="encryptionKey"
                            autofocus
                            :required="!usePrivateKey"
                        />
                    </div>

                    <PrimaryButton class="mt-4 w-full" :class="{ 'opacity-25 cursor-not-allowed': fileForm.processing || !file }"
                                   :disabled="fileForm.processing || !file">
                        <div class="w-full  h-full items-center">
                            <span class="pi pi-cloud-upload text-lg"/>
                            <!--                        <span class="ml-2">Upload</span>-->
                        </div>
                    </PrimaryButton>
                </form>
            </Card>

            <div class="flex flex-col w-1/4">
                <Card >
                    <div class="mb-2 ">
                        <span class="pi pi-file scale-150"></span>
                        <h2 class="text-lg font-semibold inline-block ml-4">My Drive</h2>
                    </div>
                    <div class="w-full bg-base-200 rounded-full h-2.5 dark:bg-base-700 overflow-hidden">
                        <div class="bg-primary-600 h-2.5 rounded-full " :style="{ width: + (storageUsage/quota)*100 + '%' }"></div>
                    </div>
                    <dt class="text-sm font-medium text-base-500 mt-1">{{ formatFileSize(storageUsage) }} / {{ formatFileSize(quota) }}</dt>
                </Card>
                <Card class="flex-1 ">
                    <div class="flex flex-col rounded-lg px-4 text-center h-full justify-center">
                        <dt class="order-last text-lg font-medium text-base-500">Total Files</dt>
                        <dd class="text-4xl font-extrabold text-primary-600 md:text-5xl">{{ filesCount }}</dd>
                    </div>
                </Card>
            </div>
        </div>

        <h1 class="text-2xl mb-4">My Files</h1>
        <Card class="flex-1">
            <Table :links="userFiles.links" :showingNumber="userFiles.data.length" :totalNumber="userFiles.total">
                <template #Head>
                    <TableHead sortable @click="sort='file_name'; sort_dir = !sort_dir;">File Name</TableHead>
                    <TableHead sortable @click="sort='size'; sort_dir = !sort_dir;">Size</TableHead>
                    <TableHead sortable @click="sort='created_at'; sort_dir = !sort_dir;">Uploaded on</TableHead>
                    <TableHead>Labels</TableHead>
                    <TableHead>{{ 'Action' }}</TableHead>
                </template>
                <template #Body>
                    <TableRow v-for="file in userFiles.data" :key="file.id">
                        <TableBodyHeader :href="'#'">{{ file.file_name }}</TableBodyHeader>
                        <TableBodyHeader :href="'#'">{{ formatFileSize(file.size) }}</TableBodyHeader>
                        <TableBody :href="'#'">{{ new Date(file.created_at).toLocaleString('en-EG')}}</TableBody>
                        <TableBody :href="'#'">
                            <Badge v-if="file.custom_properties.enc_key" color="blue">Private Key</Badge>
                            <Badge v-else color="yellow">User-defined Key</Badge>
                        </TableBody>
                        <TableBodyAction>
                            <div class=" flex gap-4" >
                                <a @click="download(file.uuid, file.file_name, file.custom_properties.enc_key, file.custom_properties.checksum)" href="#" title="Download">
                                    <span class="pi pi-cloud-download text-base-500 cursor-pointer hover:underline decoration-primary-400"/>
                                </a>
                                <a @click="download(file.uuid, file.file_name, file.custom_properties.enc_key, file.custom_properties.checksum)" href="#" title="Share">
                                    <span class="pi pi-share-alt text-base-500 cursor-pointer hover:underline decoration-primary-400"/>
                                </a>
                                <a @click="renameFile(file.uuid, file.file_name)" href="#" title="Rename File">
                                    <span class="pi pi-pen-to-square text-base-500 cursor-pointer hover:underline decoration-primary-400"/>
                                </a>
                                <a @click="deleteFile(file.uuid)" href="#" title="Delete File">
                                    <span class="pi pi-trash text-base-500 cursor-pointer hover:underline decoration-primary-400"/>
                                </a>
                            </div>
                        </TableBodyAction>
                    </TableRow>
                </template>
            </Table>
        </Card>
    </AuthenticatedLayout>
</template>
