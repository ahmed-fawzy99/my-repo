<script setup>
import {Link} from '@inertiajs/vue3';
import {initDropdowns} from "flowbite";
import {onMounted} from "vue";
import {removeContact} from "@/js-helpers/contacts-helper.js";

defineProps({
    id: {
        type: String,
        default: "card"
    },
    name: String,
    email: String,
    contactId: String,
})

onMounted(() => {
    initDropdowns();
});
</script>

<template>
    <div
        class="w-full max-w-sm bg-white border border-base-200 rounded-lg shadow dark:bg-base-800 dark:border-base-700">
        <div class="flex justify-end px-4 pt-4">
            <button :id="id+'Button'" :data-dropdown-toggle="id"
                    class="inline-block text-base-500 dark:text-base-400 hover:bg-base-100 dark:hover:bg-base-700 focus:ring-4 focus:outline-none focus:ring-base-200 dark:focus:ring-base-700 rounded-lg text-sm p-1.5"
                    type="button">
                <span class="sr-only">Open dropdown</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                     viewBox="0 0 16 3">
                    <path
                        d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/>
                </svg>
            </button>
            <!-- Dropdown menu -->
            <div :id="id"
                 class="z-10 hidden text-base list-none bg-white divide-y divide-base-100 rounded-lg shadow w-44 dark:bg-base-700">
                <ul class="py-2" :aria-labelledby="id+'Button'">
                    <li>
                        <Link @click="removeContact(contactId)" href="#"
                           class="block px-4 py-2 text-sm text-red-600 hover:bg-base-100 dark:hover:bg-base-600 dark:text-base-200 dark:hover:text-white cursor-pointer">
                            Remove
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
        <div class="flex flex-col items-center pb-10">
            <div class="flex w-24 h-24 mb-3 rounded-full shadow-lg justify-center items-center text-4xl bg-orange-500">
                <span class="pi pi-user"/>
            </div>

            <h5 class="mb-1 text-xl font-medium text-base-900 dark:text-white">{{ name }}</h5>
            <span class="text-sm text-base-500 dark:text-base-400">{{ email }}</span>
            <div class="flex mt-4 md:mt-6">
                <Link href="#" @click="$emit('openConversation', contactId)"
                      class="inline-flex items-center px-4 py-2 text-sm font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                    <span class="pi pi-send mr-2"/>Message
                </Link>
                <Link href="#"
                      class="py-2 px-4 ms-2 text-sm font-medium text-base-900 focus:outline-none bg-white rounded-lg border border-base-200 hover:bg-base-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-base-100 dark:focus:ring-base-700 dark:bg-base-800 dark:text-base-400 dark:border-base-600 dark:hover:text-white dark:hover:bg-base-700">
                    Send File
                </Link>
            </div>
        </div>
    </div>
</template>
