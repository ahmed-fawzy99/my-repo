<script setup>
import {initDropdowns} from "flowbite";
import {onMounted} from "vue";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";

dayjs.extend(relativeTime)

defineProps({
    id: {
        type: String,
        default: "card"
    },
    name: String,
    email: String,
    date: String,
})

onMounted(() => {
    initDropdowns();
});


</script>

<template>
    <div
        class="w-full max-w-sm bg-white border border-base-200 rounded-lg shadow dark:bg-base-800 dark:border-base-700">
        <div class="flex flex-col items-center py-10">
            <div class="flex w-24 h-24 mb-3 rounded-full shadow-lg justify-center items-center text-4xl">
                <span class="pi pi-user"/>
            </div>
            <h5 class="mb-1 text-xl font-medium text-base-900 dark:text-white">{{ name }}</h5>
            <span class="text-sm text-base-500 dark:text-base-400">{{ email }}</span>
            <span class="text-sm text-base-500 dark:text-base-400">{{ dayjs(date).fromNow() }}</span>
            <slot name="actions"/>

        </div>
    </div>
</template>
