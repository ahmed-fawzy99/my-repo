<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-primary-400 text-start text-base font-medium text-primary-700 bg-primary-50 focus:outline-none focus:text-primary-800 focus:bg-primary-100 focus:border-primary-700 transition duration-150 ease-in-out dark:border-primary-600 dark:text-base-200 dark:bg-primary-800 dark:focus:text-primary-300 dark:focus:bg-primary-700 dark:focus:border-primary-500'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-base-600 hover:text-base-800 hover:bg-base-50 hover:border-base-300 focus:outline-none focus:text-base-800 focus:bg-base-50 focus:border-base-300 transition duration-150 ease-in-out dark:text-base-200 dark:hover:text-base-100 dark:hover:bg-base-800 dark:focus:text-base-100 dark:focus:bg-base-800 dark:focus:border-base-600'
);

</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
