<script setup>
import {Link} from '@inertiajs/vue3';
import {initDropdowns} from "flowbite";
import {onMounted} from "vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import {contactRequestResponse} from "@/js-helpers/contacts-helper.js";

dayjs.extend(relativeTime)

defineProps({
    id: {
        type: String,
        default: "card"
    },
    name: String,
    email: String,
    date: String,
    contactId: String,
})

onMounted(() => {
    initDropdowns();
});


</script>

<template>
    <div
        class="w-full max-w-sm bg-white border border-base-200 rounded-lg shadow dark:bg-base-800 dark:border-base-700">
        <div class="flex flex-col items-center py-10">
            <div class="flex w-24 h-24 mb-3 rounded-full shadow-lg justify-center items-center text-4xl">
                <span class="pi pi-user"/>
            </div>
            <h5 class="mb-1 text-xl font-medium text-base-900 dark:text-white">{{ name }}</h5>
            <span class="text-sm text-base-500 dark:text-base-400">{{ email }}</span>
            <span class="text-sm text-base-500 dark:text-base-400">{{ dayjs(date).fromNow() }}</span>
            <div class="flex mt-4 md:mt-6">
                <Link @click="contactRequestResponse(contactId, true)" href="#"
                      class="inline-flex items-center px-4 py-2 text-sm font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                    <span class="pi pi-check mr-2"/>Accept
                </Link>
                <Link @click="contactRequestResponse(contactId, false)" href="#"
                      class="py-2 px-4 ms-2 text-sm font-medium text-base-100 focus:outline-none bg-red-600 rounded-lg border border-red-200 hover:bg-red-700 hover:text-red-100 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-700 dark:text-base-100 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-800">
                    <span class="pi pi-times mr-2"/>Reject
                </Link>
            </div>
        </div>
    </div>
</template>
