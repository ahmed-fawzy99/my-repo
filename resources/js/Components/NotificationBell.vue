<script setup>
import {onMounted} from "vue";
import {Link, usePage} from '@inertiajs/vue3';
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import {initDropdowns} from "flowbite";

dayjs.extend(relativeTime)

const notifications = usePage().props.auth.notifications;

onMounted(() => {
    initDropdowns();
});
</script>

<template>
    <button id="dropdownChatNotificationButton" data-dropdown-toggle="dropdownNotification"
            class=" text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700
                                    focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700
                                    rounded-lg text-sm p-2.5 transition-colors duration-200" type="button">

        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 14 20">
            <path d="M12.133 10.632v-1.8A5.406 5.406 0 0 0 7.979 3.57.946.946 0 0 0 8 3.464V1.1a1 1 0 0 0-2 0v2.364a.946.946 0 0 0 .021.106 5.406 5.406 0 0 0-4.154 5.262v1.8C1.867 13.018 0 13.614 0 14.807 0 15.4 0 16 .538 16h12.924C14 16 14 15.4 14 14.807c0-1.193-1.867-1.789-1.867-4.175ZM3.823 17a3.453 3.453 0 0 0 6.354 0H3.823Z"/>
        </svg>
    </button>

    <div id="dropdownNotification"
         class="z-20 hidden w-full max-w-sm bg-white divide-y divide-base-100 rounded-lg shadow dark:bg-base-950 dark:divide-base-700"
         aria-labelledby="dropdownChatNotificationButton">
        <div
            class="block px-4 py-2 font-medium text-center text-base-700 rounded-t-lg bg-base-50 dark:bg-base-950 dark:text-white">
            Notifications
        </div>
        <div class="divide-y divide-base-100 dark:divide-base-700">
            <Link v-if="notifications.length > 0" v-for="notification in notifications" :href="route('conversations.index', {contactId: notification.data.sender_id})" class="flex px-4 py-3 hover:bg-base-100 dark:hover:bg-base-800">
                <div class="flex-shrink-0">
                    <span class="pi pi-user text-2xl p-2 bg-orange-400 rounded-full" />
                </div>
                <div class="w-full ps-3">
                    <div class="text-base-500 text-sm mb-1.5 dark:text-base-400">New message from <span
                        class="font-semibold text-base-900 dark:text-white">{{notification.data.sender_name}}</span>
                    </div>
                    <div class="text-xs text-primary-600 dark:text-primary-500">{{ dayjs(notification.data.created_at).fromNow() }}</div>
                </div>
            </Link>
            <div v-else class="flex flex-col items-center justify-center p-6">
                <span class="pi pi-bell-slash p-4 rounded-full border-2 text-base-900 dark:text-base-500 border-base-900 dark:border-base-500 mb-4" />
                <p class="text-base-900 dark:text-base-500">You Have No Notifications</p>
            </div>
        </div>
    </div>
</template>
