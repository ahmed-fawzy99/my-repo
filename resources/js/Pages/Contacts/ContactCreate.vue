<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router, Link} from '@inertiajs/vue3';
import Card from "@/Components/Card.vue";

import ContactTabs from "@/Components/Tabs/ContactTabs.vue";
import ContactFind from "@/Components/ContactFind.vue";
import {ref, watch} from "vue";
import debounce from "lodash/debounce.js";

defineProps({
    allUsers: Object,
    contactRequestsCount: Number,
});

const term = ref('');
const search = debounce(() => {
    router.visit(route('contacts.create', {term: term.value}),
        {preserveState: true, preserveScroll: true})
}, 250);
watch(term, search);

const selectedContacts = ref([]);
</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #tabs>
            <ContactTabs :count="contactRequestsCount"/>
        </template>
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-4xl mb-4">Add New Contact</h1>
        </div>

        <Card>
            <h1 class="text-2xl">Find Your Contact</h1>
            <p>selected contacts: {{selectedContacts}}</p>
            <ContactFind
                :id="Math.random().toString(36).slice(2, 5)"
            >
                <template #button>
                    <span class="pi pi-user-plus mr-2" /> Add New Contact
                </template>
                <template #search>
                    <input type="text" id="contacts-search" v-model="term" class="block w-full p-2 ps-10 text-sm text-base-900 border border-base-300 rounded-lg bg-base-50 focus:ring-primary-500 focus:border-primary-500 dark:bg-base-600 dark:border-base-500 dark:placeholder-base-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Search user">
                </template>
                <template #contact>
                    <li v-for="user in allUsers.data" :key="user.id">
                        <a href="#" class="flex items-center px-4 py-2 hover:bg-base-100 dark:hover:bg-base-600 dark:hover:text-white">
                            <div class="flex items-center ps-2 rounded hover:bg-base-100 dark:hover:bg-base-600">
                                <input :id="'checkbox-'+user.id" type="checkbox" v-model="selectedContacts[key]" class="w-4 h-4 text-primary-600 bg-base-100 border-base-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-base-700 dark:focus:ring-offset-base-700 focus:ring-2 dark:bg-base-600 dark:border-base-500">
                                <span class="pi pi-user ml-4"/>
                                <label :for="'checkbox-'+user.id" class="w-full py-2 ms-2 text-sm font-medium text-base-900 rounded dark:text-base-300">{{ user.name }}</label>
                            </div>
                        </a>
                    </li>
                </template>
                <template #selected>

                </template>
            </ContactFind>
        </Card>

    </AuthenticatedLayout>
</template>
