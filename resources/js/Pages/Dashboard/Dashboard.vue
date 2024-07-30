<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {onMounted, ref, watch} from "vue";
import {Head, router, useForm} from '@inertiajs/vue3';
import debounce from "lodash/debounce";

import {formatFileSize} from "@/js-helpers/generic-helpers.js";
import {
    downloadDecrypted,
    generateKey,
    removeFile,
    renameFile,
    share,
    uploadDecrypted,
} from "@/js-helpers/crypto-helpers.js";

import Card from "@/Components/Card.vue";
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
import ToolTip from "@/Components/ToolTip.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Badge from "@/Components/Badge.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DashboardTabs from "@/Components/Tabs/DashboardTabs.vue";

const usePrivateKey = ref(true);
const encryptionKey = ref('');
const sort = ref('file_name');
const sort_dir = ref(true);
const fileForm = useForm({file: null, checksum: '', name: '', type: '', key: '', choice: true});
const props = defineProps({
    userFiles: Object,
    filesCount: Number,
    storageUsage: Number,
    quota: Number,
});

const upload = () => uploadDecrypted(fileForm, usePrivateKey, encryptionKey);
const reset = () => {
    fileForm.reset();
    usePrivateKey.value = true;
    encryptionKey.value = '';
    document.getElementById('file-input').value = '';
}

const shareHandler = (uuid, encStatus) => {
    if (encStatus) {
        return false;
    }
    share(uuid);
}

const search = debounce(() => {
    router.visit(route('dashboard', {sort: sort.value, sort_dir: sort_dir.value}),
        {preserveState: true, preserveScroll: true})
}, 100);
watch(sort, search);
watch(sort_dir, search);

onMounted(() => {
    document.getElementById('file-input').addEventListener('change', (e) => {
        fileForm.file = e.target.files[0];
        fileForm.name = e.target.files[0].name;
        fileForm.type = e.target.files[0].type;
    });
});
</script>

<template>
    <Head title="Dashboard"/>
    <AuthenticatedLayout>
        <template #tabs>
            <DashboardTabs/>
        </template>
        <h1 class="text-4xl mb-4 ms-4 md:ms-0">Dashboard</h1>
        <div class="grid grid-cols-12 gap-4">
            <Card class="col-span-12 md:col-span-9">
                <h2 class="text-xl">
                    Add File
                </h2>
                <form @submit.prevent="upload" class="pt-4">
                    <FileInput no-label v-model="fileForm.file"/>
                    <Toggle class="mt-4" v-model="usePrivateKey"
                            label="Encrypt Using My Private Key (Disable If Sharing)"/>
                    <div v-if="!usePrivateKey">
                        <InputLabel for="enc-choice" value="Specific Encryption Key" class="inline"/>
                        <span class="pi pi-refresh ml-2 cursor-pointer text-xs" title="Generate Random Key"
                              @click="encryptionKey = generateKey(16)"/>
                        <ToolTip direction="right">This is the key that will be used to decrypt the file.
                            If you are going to share this file, this is the key you will share with the recipient(s).
                            Don't lose it!
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
                    <div class="flex gap-4">
                        <PrimaryButton class="mt-4 w-3/4"
                                       :class="{ 'opacity-25 cursor-not-allowed': fileForm.processing || !fileForm.file }"
                                       :disabled="fileForm.processing || !fileForm.file">
                            <div class="flex w-full items-center justify-center">
                                <span class="pi pi-cloud-upload text-lg"/>
                                <span class="ml-2">Upload</span>
                            </div>
                        </PrimaryButton>
                        <SecondaryButton class="mt-4 w-1/4"
                                         :class="{ 'opacity-25 cursor-not-allowed': fileForm.processing || !fileForm.file }"
                                         :disabled="fileForm.processing || !fileForm.file"
                                         @click="reset">
                            <div class="flex w-full items-center justify-center">
                                <span class="pi pi-eraser text-lg"/>
                                <span class="ml-2">Reset</span>
                            </div>
                        </SecondaryButton>
                    </div>
                </form>
            </Card>

            <div class="col-span-12 md:col-span-3 flex flex-col">
                <Card>
                    <div class="mb-2 ">
                        <span class="pi pi-file scale-150"></span>
                        <h2 class="text-lg font-semibold inline-block ml-4">My Drive</h2>
                    </div>
                    <div class="w-full bg-base-200 rounded-full h-2.5 dark:bg-base-700 overflow-hidden">
                        <div class="bg-primary-600 h-2.5 rounded-full "
                             :style="{ width: + (storageUsage/quota)*100 + '%' }"></div>
                    </div>
                    <dt class="text-sm font-medium text-base-500 mt-1">{{ formatFileSize(storageUsage) }} /
                        {{ formatFileSize(quota) }}
                    </dt>
                </Card>
                <Card class="flex-1 ">
                    <div class="flex flex-col rounded-lg px-4 text-center h-full justify-center">
                        <dt class="order-last text-lg font-medium text-base-500">Total Files</dt>
                        <dd class="text-4xl font-extrabold text-primary-600 md:text-5xl">{{ filesCount }}</dd>
                    </div>
                </Card>
            </div>
        </div>

        <div class="w-full">

        </div>
        <h1 class="text-2xl mb-4 ms-4 md:ms-0">My Files</h1>
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
                        <TableBody :href="'#'">{{ new Date(file.created_at).toLocaleString('en-EG') }}</TableBody>
                        <TableBody :href="'#'">
                            <Badge v-if="file.custom_properties.enc_key" color="blue">Private Key</Badge>
                            <Badge v-else color="yellow">User-defined Key</Badge>
                        </TableBody>
                        <TableBodyAction>
                            <div class=" flex gap-4 text-xl">
                                <a @click="downloadDecrypted(file.uuid, file.file_name, file.custom_properties.enc_key, file.custom_properties.checksum)"
                                   href="#" title="Download">
                                    <span
                                        class="pi pi-cloud-download text-base-500 cursor-pointer hover:bg-primary-300 hover:dark:bg-primary-600 dark:text-base-100 rounded-lg transition ease-in-out duration-150 p-2"/>
                                </a>
                                <a @click="shareHandler(file.uuid, file.custom_properties.enc_key)">
                                                    <span
                                                        :class="{'text-base-500/30 cursor-not-allowed dark:text-base-100/20' : file.custom_properties.enc_key,
                                                                 'text-base-500 cursor-pointer hover:bg-primary-300 hover:dark:bg-primary-600 dark:text-base-100 transition ease-in-out duration-150': !file.custom_properties.enc_key}"
                                                        class="pi pi-share-alt rounded-lg transition ease-in-out duration-150 p-2"/>
                                </a>
                                <a @click="renameFile(file.uuid, file.file_name)" href="#" title="Rename File">
                                    <span class="pi pi-pen-to-square rounded-lg p-2"/>
                                </a>
                                <a @click="removeFile(file.uuid)" href="#" title="Remove File">
                                    <span
                                        class="pi pi-trash text-base-500 cursor-pointer hover:bg-primary-300 hover:dark:bg-primary-600 dark:text-base-100 rounded-lg transition ease-in-out duration-150 p-2"/>
                                </a>
                            </div>
                        </TableBodyAction>
                    </TableRow>
                </template>
            </Table>
        </Card>

    </AuthenticatedLayout>
</template>
