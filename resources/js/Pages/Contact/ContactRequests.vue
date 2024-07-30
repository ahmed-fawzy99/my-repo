<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import Card from "@/Components/Card.vue";
import ContactTabs from "@/Components/Tabs/ContactTabs.vue";
import ContactRequestCard from "@/Components/ContactRequestCard.vue";
import {contactRequestResponse} from "@/js-helpers/contacts-helper.js";

defineProps({
    contactRequests: Object,
    sentRequestsCount: Number,
});

</script>

<template>
    <Head title="Pending Requests"/>

    <AuthenticatedLayout>
        <template #tabs>
            <ContactTabs :inCount="contactRequests.length" :outCount="sentRequestsCount"/>
        </template>
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h1 class="text-4xl mb-4">Pending Requests</h1>
        </div>
        <Card>
            <div v-if="contactRequests.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mx-auto">
                <ContactRequestCard v-for="contact in contactRequests" :key="contact.id"
                    :id="Math.random().toString(36).slice(2, 5)"
                    :name="contact.user.name" :email="contact.user.email" :date="contact.created_at"
                >
                    <template #actions>
                        <div class="flex mt-4 md:mt-6">
                            <Link @click="contactRequestResponse(contact.user.id, true)" href="#"
                                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                <span class="pi pi-check mr-2"/>Accept
                            </Link>
                            <Link @click="contactRequestResponse(contact.user.id, false)" href="#"
                                  class="py-2 px-4 ms-2 text-sm font-medium text-base-100 focus:outline-none bg-red-600 rounded-lg border border-red-200 hover:bg-red-700 hover:text-red-100 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-700 dark:text-base-100 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-800">
                                <span class="pi pi-times mr-2"/>Reject
                            </Link>
                        </div>
                    </template>
                </ContactRequestCard>
            </div>
            <div v-else class="flex h-32 justify-center items-center text-xl">
                <span class="pi pi-check-circle mr-2" /> No pending contact requests.
            </div>
        </Card>

    </AuthenticatedLayout>
</template>
