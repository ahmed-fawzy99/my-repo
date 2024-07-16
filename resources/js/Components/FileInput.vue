<script setup>
defineProps({
    label: {
        type: String,
        required: false,
        default: 'Choose File',
    },
    noLabel: {
        type: Boolean,
        required: false,
        default: false,
    },
    id: {
        type: String,
        required: false,
        default: 'file-input',
    },
});

const model = defineModel();

const onChange = (e) => {
    model.value = e.target.files[0];
};

</script>

<template>
    <label v-if="!noLabel" class="block mb-2 text-sm font-medium text-base-900 dark:text-white" :for="id">{{ label }}</label>
    <input class="w-full text-base-900 dark:text-base-100 dark:bg-gray-700 border-base-300 focus:border-primary-500 focus:ring-primary-500 dark:border-base-900 dark:focus:border-primary-800 dark:focus:ring-primary-800  rounded-md shadow-sm"
           :id="id" type="file" @change="onChange($event)" >
</template>
