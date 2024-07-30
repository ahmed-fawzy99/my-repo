<script setup>
import NavLink from "@/Components/NavLink.vue";
import GoBackNavLink from "@/Components/GoBackNavLink.vue";

defineProps({
    inCount: Number,
    outCount: Number,
})
</script>

<template>
    <GoBackNavLink/>

    <NavLink :href="route('contacts.index')" :active="route().current('contacts.index')" class="break-all">
        My Contacts
    </NavLink>
    <NavLink :href="route('contacts-requests')" :active="route().current('contacts-requests')" class="break-all">
        Contact Requests
        <span v-if="inCount>0" class="ml-2 bg-primary-100 text-primary-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-primary-900 dark:text-primary-300">{{ inCount }}</span>
    </NavLink>
    <NavLink :href="route('contacts-sent-requests')" :active="route().current('contacts-sent-requests')" class="break-all">
        Sent Requests
        <span v-if="outCount>0" class="ml-2 bg-primary-100 text-primary-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-primary-900 dark:text-primary-300">{{ outCount }}</span>
    </NavLink>


</template>
