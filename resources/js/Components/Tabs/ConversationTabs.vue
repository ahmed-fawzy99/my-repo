<script setup>
import NavLink from "@/Components/NavLink.vue";

defineProps({
    inCount: Number,
    outCount: Number,
})
</script>

<template>
<!--    <GoBackNavLink/>-->

    <NavLink :href="route('conversations.index')" :active="route().current('conversations.index')">
        My Conversations
    </NavLink>
</template>
