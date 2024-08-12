<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
<!--        <div>-->
<!--            <Link href="/">-->
<!--                <ApplicationLogo class="w-64 px-6 py-4 bg-white dark:bg-base-800 shadow-md rounded-lg fill-current text-gray-500" />-->
<!--            </Link>-->
<!--        </div>-->
        <div
            class="w-full sm:max-w-2xl mt-6 p-8 bg-white dark:bg-base-800 shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>

        <footer class="mt-2">
            <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                <p>&copy; 2024 All rights reserved by MyRepo.</p>
            </div>
        </footer>
    </div>

</template>
