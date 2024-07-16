<script setup>
defineProps({
    label: {
        type: String,
        required: false,
    },
})
const model = defineModel({
    type: Boolean,
    required: true,
})
</script>

<template>
    <label class="inline-flex items-center cursor-pointer">
        <input type="checkbox" class="sr-only peer" v-model="model">
        <div class="relative w-11 h-6 bg-base-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-base-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-base-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-base-600 peer-checked:bg-primary-600"></div>
        <span v-if="label" class="ms-3 text-sm font-medium text-base-900 dark:text-base-300">{{ label }}</span>
    </label>
</template>
