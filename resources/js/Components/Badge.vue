<script setup>

const props = defineProps({
    color: {
        type: String,
        default: 'indigo'
    },
});
const className = `bg-${props.color}-100 text-${props.color}-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded dark:bg-${props.color}-900 dark:text-${props.color}-300`;
</script>

<template>
    <span :class="className">
        <slot />
    </span>

</template>

<style scoped>

</style>
