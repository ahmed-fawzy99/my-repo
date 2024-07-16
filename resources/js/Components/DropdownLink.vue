<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-base-700 dark:text-base-200 hover:bg-base-100 dark:hover:bg-base-800 focus:outline-none focus:bg-base-100 transition duration-150 ease-in-out"
    >
        <slot />
    </Link>
</template>
