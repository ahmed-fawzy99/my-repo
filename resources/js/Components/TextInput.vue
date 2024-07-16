<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="text-base-900 dark:text-base-100 dark:bg-gray-900 border-base-300 focus:border-primary-500 focus:ring-primary-500 dark:border-base-900 dark:focus:border-primary-800 dark:focus:ring-primary-800  rounded-md shadow-sm "
        v-model="model"
        ref="input"
    />
</template>
