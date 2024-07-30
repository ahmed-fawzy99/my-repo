<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router, Link} from '@inertiajs/vue3';
import Card from "@/Components/Card.vue";
import ContactCard from "@/Components/ContactCard.vue";
import ContactTabs from "@/Components/Tabs/ContactTabs.vue";
import {ref, watch} from "vue";
import debounce from "lodash/debounce.js";
import ContactFind from "@/Components/ContactFind.vue";
import {sendContactRequest} from "@/js-helpers/contacts-helper.js";
import {sendFile} from "@/js-helpers/crypto-helpers.js";

const props = defineProps({
    contacts: Object,
    allUsers: Object,
    contactRequestsCount: Number,
    sentRequestsCount: Number,
});

const term = ref('');
const search = debounce(() => {
    router.visit(route('contacts.index', {term: term.value}),
        {preserveState: true, preserveScroll: true});
}, 250);
watch(term, search);

const selectedContacts = ref([]);

const openConversation = (contactId) => {
    router.get(route('conversations.index', {contactId}),
        {preserveState: true, preserveScroll: true});
}
const trackSelectedContacts = (event, id, name) => {
    if (event.target.checked) {
        selectedContacts.value.push({id, name});
    } else {
        selectedContacts.value = selectedContacts.value.filter(user => user.id !== id);
    }
}
</script>

<template>
    <Head title="My Contacts"/>

    <AuthenticatedLayout>
        <template #tabs>
            <ContactTabs :inCount="contactRequestsCount" :outCount="sentRequestsCount"/>
        </template>
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-4xl mb-4">My Contacts</h1>
            <div class="flex gap-4">
                <ContactFind
                    v-model="selectedContacts"
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
                                    <input :id="'checkbox-'+user.id" type="checkbox" :checked="selectedContacts.some(item => item.id === user.id)" :value="user.id" @change="trackSelectedContacts($event, user.id, user.name)"
                                           class="w-4 h-4 text-primary-600 bg-base-100 border-base-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-base-700 dark:focus:ring-offset-base-700 focus:ring-2 dark:bg-base-600 dark:border-base-500">
                                    <span class="pi pi-user ml-4 "/>
                                    <label :for="'checkbox-'+user.id" class="w-full py-2 ms-2 text-sm font-medium text-base-900 rounded dark:text-base-300">{{ user.name }}</label>
                                </div>
                            </a>
                        </li>
                    </template>
                    <template #action>
                        <Link v-if="selectedContacts.length" @click="sendContactRequest(selectedContacts.map(contact => contact.id)); selectedContacts = []; " href="#"
                              class="flex items-center p-3 text-sm font-medium text-primary-600 dark:text-primary-400 border-t border-base-200 rounded-b-lg bg-base-50 dark:border-base-600 hover:bg-base-100 dark:bg-base-700 dark:hover:bg-base-600  hover:underline">
                            <span class="pi pi-user-plus mr-2" />
                            Send a Request
                        </Link>
                    </template>
                </ContactFind>
            </div>
        </div>

        <Card>
            <div v-if="contacts.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mx-auto">
                <ContactCard v-for="contact in contacts" :key="contact.id"
                    :id="Math.random().toString(36).slice(2, 5)" @openConversation="openConversation" @sendFile="sendFile"
                    :name="contact.name" :email="contact.email" :contactId="contact.id"
                />
            </div>
            <div v-else class="flex h-32 justify-center items-center text-xl">
                <span class="pi pi-info-circle mr-2" /> You don't have any contacts.
            </div>
        </Card>

    </AuthenticatedLayout>
</template>
