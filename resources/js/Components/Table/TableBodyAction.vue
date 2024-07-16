<script setup>
import {Link} from "@inertiajs/vue3";

defineProps({
    href: String,
})

</script>

<template>
    <td class="px-6 py-4 font-medium text-purple-600 dark:text-purple-500">
        <slot />
    </td>
</template>
