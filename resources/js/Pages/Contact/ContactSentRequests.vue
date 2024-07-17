<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import Card from "@/Components/Card.vue";
import ContactTabs from "@/Components/Tabs/ContactTabs.vue";
import ContactRequestCard from "@/Components/ContactRequestCard.vue";
import {cancelContactRequest} from "@/js-helpers/contacts-helper.js";

defineProps({
    contactSentRequests: Object,
    contactRequestsCount: Number,
});

</script>

<template>
    <Head title="Sent Requests"/>

    <AuthenticatedLayout>
        <template #tabs>
            <ContactTabs :inCount="contactRequestsCount" :outCount="contactSentRequests.length"/>
        </template>
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-4xl mb-4">Pending Sent Requests</h1>
        </div>
        <Card>
            <div v-if="contactSentRequests.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mx-auto">
                <ContactRequestCard v-for="request in contactSentRequests" :key="request.id"
                    :id="Math.random().toString(36).slice(2, 5)"
                    :name="request.contact.name" :email="request.contact.email" :date="request.created_at"
                >
                    <template #actions>
                        <div class="flex mt-4 md:mt-6">
                            <Link @click="cancelContactRequest(request.contact.id)" href="#"
                                  class="py-2 px-4 ms-2 text-sm font-medium text-base-100 focus:outline-none bg-red-600 rounded-lg border border-red-200 hover:bg-red-700 hover:text-red-100 focus:z-10 focus:ring-4 focus:ring-red-100 dark:focus:ring-red-700 dark:bg-red-700 dark:text-base-100 dark:border-red-600 dark:hover:text-white dark:hover:bg-red-800">
                                <span class="pi pi-times mr-2"/>Cancel Request
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
