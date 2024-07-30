<script setup>
import { Link } from '@inertiajs/vue3';

function back() {
    window.history.back();
}
</script>

<template>
  <Link @click="back" href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-base-500 dark:text-base-100 hover:text-base-700 hover:border-base-300 dark:hover:text-base-400 dark:hover:border-base-300 focus:outline-none focus:text-base-700 focus:border-base-300 transition duration-150 ease-in-out">
    <span class="pi pi-arrow-left me-3"/>Back
  </Link>
</template>
