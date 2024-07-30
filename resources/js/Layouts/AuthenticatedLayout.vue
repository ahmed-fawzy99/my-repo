<script setup>
import {reactive, ref} from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import {Link, usePage} from '@inertiajs/vue3';
import 'primeicons/primeicons.css'
import {useDark, useToggle} from "@vueuse/core";
import ChatNotificationBell from "@/Components/NotificationBell.vue";
import ToastNotification from "@/Components/ToastNotification.vue";
import {Toast} from "@/js-helpers/generic-helpers.js";

const isDark = useDark();
const toggleDark = useToggle(isDark);
const page = usePage()
const audio = new Audio('storage/sounds/incoming-notification.mp3');
const notification = ref({});

const showingNavigationDropdown = ref(false);
const usageWarningStatus = ref(localStorage.getItem('usageWarning'));
const setUsageWarning = () => {
    usageWarningStatus.value = 'seen';
    localStorage.setItem('usageWarning', 'seen')
};


Echo.private('App.Models.User.' + page.props.auth.user.id)
    .notification((n) => {
        notification.value = n;

        page.props.auth.notifications.unshift({data:n});
        page.props.auth.notifications.splice(-1, 1);

        if (route().current('conversations.index')){
            return;
        }

        audio.play();
        const toast = document.getElementById('toast-notification');
        // We need a better toaster
        if (toast.classList.contains('hidden')){
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
                notification.value = {};
            }, 3000);
        }

    });

defineProps({
    freeContent: {
        type: Boolean,
        default: false
    },
});

</script>

<template>
    <div class="flex flex-row">
        <aside
            class="hidden md:block md:fixed top-0 z-40 items-center w-16 h-full text-base-400 bg-base-300 dark:bg-base-950 shadow-sm">
            <div class="flex flex-col justify-between h-full">
                <div class="flex flex-col h-full">
                    <Link class="flex items-center justify-center pt-3 pb-2" :href="route('dashboard')">
                        <svg class="w-8 h-8 text-base-700 dark:text-base-100" xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 20 20"
                             fill="currentColor">
                            <path
                                d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17zM15.211 6.276a1 1 0 000-1.788l-4.764-2.382a1 1 0 00-.894 0L4.789 4.488a1 1 0 000 1.788l4.764 2.382a1 1 0 00.894 0l4.764-2.382zM4.447 8.342A1 1 0 003 9.236V15a1 1 0 00.553.894l4 2A1 1 0 009 17v-5.764a1 1 0 00-.553-.894l-4-2z"/>
                        </svg>

                    </Link>
                    <div class="flex flex-col items-center mt-3 border-t border-base-700 dark:border-base-700 h-full">
                        <Link
                            class="flex items-center justify-center w-12 h-12 mt-2 rounded
                            hover:bg-base-400 dark:hover:bg-base-700"
                            :class="{'bg-base-400 dark:bg-primary-700': route().current('contacts.index')}"
                            :href="route('contacts.index')">
                            <span class="pi pi-users scale-125 text-base-700 dark:text-base-200"/>
                        </Link>
                        <Link
                            class="flex items-center justify-center w-12 h-12 mt-2 rounded
                            hover:bg-base-400 dark:hover:bg-base-700"
                            :class="{'bg-base-400 dark:bg-primary-700': route().current('conversations.index')}"
                            :href="route('conversations.index')">
                            <span class="pi pi-inbox scale-125 text-base-700 dark:text-base-200"/>
                        </Link>
                    </div>

                </div>
                <div class="flex flex-col items-center border-base-700 mb-2">
                    <Link
                        class="flex items-center justify-center w-12 h-12 mt-2 rounded
                            hover:bg-base-400 dark:hover:bg-base-700"
                        :class="{'bg-base-400 dark:bg-primary-700': route().current('profile.edit')}"
                        :href="route('profile.edit')">
                        <span class="pi pi-cog scale-125 text-base-700 dark:text-base-200"/>
                    </Link>
                    <Link
                        class="flex items-center justify-center w-12 h-12 mt-2 rounded
                         hover:bg-base-400 dark:hover:bg-base-700"
                        :href="route('logout')" method="post" as="button">
                        <span class="pi pi-sign-out scale-125 text-base-700 dark:text-base-200"/>
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Mobile Nav -->
        <div class="fixed md:hidden bottom-0 w-full h-12 z-40 items-center  text-base-400 bg-base-300 dark:bg-base-950 shadow-sm">
            <div class="flex flex-row h-full">
                <Link class="flex items-center justify-center px-4 border-r dark:border-gray-800 mr-1" :href="route('dashboard')">
                    <svg class="w-8 h-8 text-base-700 dark:text-base-100" xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 20 20"
                         fill="currentColor">
                        <path
                            d="M11 17a1 1 0 001.447.894l4-2A1 1 0 0017 15V9.236a1 1 0 00-1.447-.894l-4 2a1 1 0 00-.553.894V17zM15.211 6.276a1 1 0 000-1.788l-4.764-2.382a1 1 0 00-.894 0L4.789 4.488a1 1 0 000 1.788l4.764 2.382a1 1 0 00.894 0l4.764-2.382zM4.447 8.342A1 1 0 003 9.236V15a1 1 0 00.553.894l4 2A1 1 0 009 17v-5.764a1 1 0 00-.553-.894l-4-2z"/>
                    </svg>

                </Link>
                <div class="flex flex-row items-center w-full">
                    <Link
                        class="flex items-center justify-center w-12 h-12  rounded
                            hover:bg-base-400 dark:hover:bg-base-700"
                        :class="{'bg-base-400 dark:bg-primary-700': route().current('contacts.index')}"
                        :href="route('contacts.index')">
                        <span class="pi pi-users text-2xl text-base-700 dark:text-base-200"/>
                    </Link>
                    <Link
                        class="flex items-center justify-center w-12 h-12  rounded
                            hover:bg-base-400 dark:hover:bg-base-700"
                        :class="{'bg-base-400 dark:bg-primary-700': route().current('conversations.index')}"
                        :href="route('conversations.index')">
                        <span class="pi pi-inbox scale-125 text-base-700 dark:text-base-200"/>
                    </Link>
                </div>
                <div class="flex flex-row items-center border-base-700 border-l dark:border-gray-800">
                    <Link
                        class="flex items-center justify-center w-12 h-12  rounded
                            hover:bg-base-400 dark:hover:bg-base-700"
                        href="#">
                        <span v-if="isDark" class="pi pi-sun scale-125 text-base-700 dark:text-base-200" @click="toggleDark()"/>
                        <span v-else class="pi pi-moon scale-125 text-base-700 dark:text-base-200" @click="toggleDark()"/>
                    </Link>
                    <Link
                        class="flex items-center justify-center w-12 h-12  rounded
                            hover:bg-base-400 dark:hover:bg-base-700"
                        :class="{'bg-base-400 dark:bg-primary-700': route().current('profile.edit')}"
                        :href="route('profile.edit')">
                        <span class="pi pi-cog scale-125 text-base-700 dark:text-base-200"/>
                    </Link>
                    <Link
                        class="flex items-center justify-center w-12 h-12  rounded
                         hover:bg-base-400 dark:hover:bg-base-700"
                        :href="route('logout')" method="post" as="button">
                        <span class="pi pi-sign-out scale-125 text-base-700 dark:text-base-200"/>
                    </Link>
                </div>

            </div>
        </div>

        <Transition>
            <div v-if="!usageWarningStatus"
                 class="fixed z-50 w-full h-8 bottom-0 text-center ml-16 bg-amber-300 dark:bg-amber-500  ">
                <p class="h-full content-center text-base-900">
                    <span class="pi pi-exclamation-triangle"/> This is a demo site, uploads have been limited to 10 files
                    and 5 MB max. each. Files are subject to deletion at any time.
                    <span class="pi pi-times text-xs cursor-pointer" @click="setUsageWarning()"></span>
                </p>
            </div>
        </Transition>

        <div class="md:ml-16 min-h-screen flex-grow bg-base-100 dark:bg-base-900">
            <nav class="bg-white dark:bg-base-900 border-b border-base-100 dark:border-base-700  shadow-sm">
                <!-- Primary Navigation Menu -->
                <div class=" mx-auto px-4 sm:px-6 lg:px-8" :class="{'max-w-7xl' : !freeContent}">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Navigation Links -->
                            <div class="space-x-4 md:space-x-8 md:-my-px flex overflow-hidden">
                                <slot name="tabs"></slot>
                            </div>
                        </div>

                        <div class="hidden md:flex sm:items-center sm:ms-6">

                            <!-- Dropdown menu -->
                            <ChatNotificationBell/>
                            <button
                                @click="toggleDark()"
                                class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700
                                    focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700
                                    rounded-lg text-sm p-2.5 transition-colors duration-200 ml-2"
                            >
                                <svg
                                    id="theme-toggle-dark-icon"
                                    class="w-5 h-5"
                                    :class="isDark ? 'hidden' : 'block'"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"
                                    ></path>
                                </svg>
                                <svg
                                    id="theme-toggle-light-icon"
                                    class="w-5 h-5"
                                    :class="isDark ? 'block' : 'hidden'"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                    ></path>
                                </svg>
                            </button>

                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-base-500 dark:text-base-100 bg-white dark:bg-base-900 hover:text-base-700 dark:hover:text-base-300 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> Profile</DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="md:hidden bg-base-950"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('account-data')" :active="route().current('account-data')">
                            Account Data
                        </ResponsiveNavLink>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white dark:bg-base-800 shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header"/>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot name="free-content"/>
                <div v-if="!freeContent" class="py-8">
                    <ToastNotification v-model="notification"/>
                    <div class="w-screen md:max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <slot/>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.v-enter-active {
    transition: all 0.3s ease-out;
}

.v-leave-active {
    transition: all 0.4s cubic-bezier(1, 0.5, 0.8, 1);
}

.v-enter-from,
.v-leave-to {
    transform: translateY(20px);
    opacity: 0;
}
</style>
