<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div>
            <Link href="/">
                <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="max-w-full mx-4 sm:max-w-2xl  mt-6 px-6 py-4 bg-white dark:bg-base-800 shadow-md overflow-hidden rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
