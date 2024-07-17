<script setup>
import NavLink from "@/Components/NavLink.vue";

defineProps({
    count: Number
})
</script>

<template>
<!--    <GoBackNavLink/>-->

    <NavLink :href="route('contacts.index')" :active="route().current('contacts.index')">
        My Contacts
    </NavLink>
    <NavLink :href="route('contacts.create')" :active="route().current('contacts.create')">
        Add Contact
    </NavLink>
    <NavLink :href="route('contacts-requests')" :active="route().current('contacts-requests')">
        Contact Requests
        <span v-if="count>0" class="ml-2 bg-primary-100 text-primary-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-primary-900 dark:text-primary-300">{{ count }}</span>
    </NavLink>


</template>
