<script setup>
import NavLink from "@/Components/NavLink.vue";
</script>

<template>
<!--    <GoBackNavLink/>-->

    <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
        Dashboard
    </NavLink>
    <NavLink :href="route('key-vault')" :active="route().current('key-vault')">
        Key Vault
    </NavLink>
    <NavLink :href="route('account-data')" :active="route().current('account-data')">
        Account Data
    </NavLink>


</template>
