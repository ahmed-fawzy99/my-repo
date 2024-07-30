<script setup>
import {Link} from '@inertiajs/vue3';
import {onMounted} from "vue";
import {initDropdowns} from "flowbite";
import {copyToClipboard} from "@/js-helpers/generic-helpers.js";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
dayjs.extend(relativeTime)
defineProps({
    id: String,
    isSender: {
        type: Boolean,
        default: true,
    },
    msg: Object,
})

onMounted(() => {
    initDropdowns();
});



</script>

<template>

    <div class="flex items-start gap-2.5"
         :class="{
                'flex-row-reverse': isSender,
            }"
    >
        <span class="pi pi-user text-2xl text-white rounded-full p-1 shadow-sm"
              :class="{
                    'bg-primary-600 shadow-primary-600/30': isSender,
                    'bg-orange-500 shadow-orange-600/30': !isSender,
                }"
        />

        <div v-if="!msg.processing"  class="flex flex-col w-full max-w-[320px] leading-1.5 p-4 border-base-200 "
             :class="{
                    'rounded-e-xl rounded-es-xl bg-orange-600': !isSender,
                    'rounded-s-xl rounded-ee-xl bg-primary-700 ': isSender,
                }">
            <span class="text-sm font-semibold text-white">{{ msg.name }}</span>
            <span class="text-xs font-normal text-base-300">{{ dayjs(msg.time).fromNow()}} <span class="hidden md:inline">{{'- ' + dayjs(msg.time).format('YYYY-MM-DD h:mm A') }}</span></span>


            <p v-if="msg.content" class="text-sm font-normal py-2.5 text-white break-words">
                <span v-if="msg.encrypted">
                    🔒 <span class="italic">[Encrypted Message]</span>
                </span>
                <span v-else>
                    {{ msg.content }}
                </span>
            </p>
            <p v-else class="text-sm italic py-2.5 text-white">
                [Message Deleted]
            </p>
            <span v-if="msg.read && msg.sender_id === msg.auth_id" class="text-xs font-normal text-base-100">Seen</span>
        </div>

        <!-- Processing -->
        <div v-else  class="flex flex-col w-full max-w-[320px] leading-1.5 p-4 border-base-200"
             :class="{
                    'rounded-e-xl rounded-es-xl bg-orange-600': !isSender,
                    'rounded-s-xl rounded-ee-xl bg-primary-700 ': isSender,
                }">
            <div class="animate-pulse">
                <div class="h-1.5 bg-base-300 rounded-full w-full mb-1"></div>
                <div class="h-1.5 bg-base-300 rounded-full w-full mb-1"></div>
                <div class="h-1.5 bg-base-300 rounded-full w-1/2 mb-1"></div>
                <p class="text-sm mt-2">Decrypting...</p>
            </div>

        </div>

        <button :id="id+'IconButton'" :data-dropdown-toggle="id" data-dropdown-placement="bottom-start"
                class="inline-flex self-center items-center p-2 text-sm font-medium text-center text-base-900 bg-white rounded-lg hover:bg-base-100 focus:ring-4 focus:outline-none dark:text-white focus:ring-base-50 dark:bg-base-900 dark:hover:bg-base-800 dark:focus:ring-base-600"
                type="button">
            <svg class="w-4 h-4 text-base-500 dark:text-base-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                 fill="currentColor" viewBox="0 0 4 15">
                <path
                    d="M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
            </svg>
        </button>
        <div :id="id"
             class="z-10 hidden bg-white divide-y divide-base-100 rounded-lg shadow w-40 dark:bg-base-700 dark:divide-base-600">
            <ul class="py-2 text-sm text-base-700 dark:text-base-200" :aria-labelledby="id+'IconButton'">
                <li>
                    <a @click="copyToClipboard(msg.content)" href="#" class="block px-4 py-2 hover:bg-base-100 dark:hover:bg-base-600 dark:hover:text-white">Copy</a>
                </li>
                <li v-if="msg.sender_id === msg.auth_id && msg.content !== ''">
                    <a @click="$emit('deleteMessage', msg.id)" href="#" class="block px-4 py-2 hover:bg-base-100 dark:hover:bg-base-600 dark:hover:text-white">Delete</a>
                </li>
            </ul>
        </div>
    </div>

</template>

<style scoped>

</style>
