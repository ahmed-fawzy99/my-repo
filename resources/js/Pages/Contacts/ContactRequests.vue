<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import Card from "@/Components/Card.vue";
import ContactTabs from "@/Components/Tabs/ContactTabs.vue";
import ContactRequestCard from "@/Components/ContactRequestCard.vue";

defineProps({
    contactRequests: Object,
});

</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #tabs>
            <ContactTabs :count="contactRequests.length" />
        </template>
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-4xl mb-4">Pending Contact Requests</h1>
        </div>
        <Card>
            <div v-if="contactRequests.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mx-auto">
                <ContactRequestCard v-for="contact in contactRequests" :key="contact.id"
                    :id="Math.random().toString(36).slice(2, 5)"
                    :name="contact.user.name" :email="contact.user.email" :date="contact.created_at" :contactId="contact.user.id"
                />
            </div>
            <div v-else class="flex h-32 justify-center items-center text-xl">
                <span class="pi pi-check-circle mr-2" /> No pending contact requests.
            </div>

        </Card>

    </AuthenticatedLayout>
</template>
