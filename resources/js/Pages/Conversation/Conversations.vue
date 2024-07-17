<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import Card from "@/Components/Card.vue";
import ConversationTabs from "@/Components/Tabs/ConversationTabs.vue";

defineProps({
    conversations: Object,
});
</script>

<template>
    <Head title="My Conversations"/>
    <AuthenticatedLayout>
        <template #tabs>
            <ConversationTabs/>
        </template>

        <div class="!h-full !w-full !bg-red-400">

        </div>

    </AuthenticatedLayout>
</template>
