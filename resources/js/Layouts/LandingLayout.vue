<script setup>
import {Link} from '@inertiajs/vue3';
import {useDark, useToggle} from "@vueuse/core";

const isDark = useDark();
const toggleDark = useToggle(isDark);
</script>

<template>
    <div class="flex flex-col h-screen">
        <header>
            <nav class="min-w-max h-16 bg-base-100 dark:bg-base-800 border-base-200 px-4 lg:px-6 py-2.5 ">
                <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-xl">
                    <Link :href="route('home')" class="flex items-center">
                        <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">MyRepo</span>
                    </Link>
                    <div class="flex items-center order-2">
                        <Link v-if="!$page.props.auth.user" :href="route('login')"
                              class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 lg:px-5 py-2 lg:py-2.5 mr-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                            Log in
                        </Link>
                        <Link
                            v-else
                            :href="route('dashboard')"
                            class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 lg:px-5 py-2 lg:py-2.5 mr-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                            Dashboard
                        </Link>
                    </div>
                    <div class="hidden justify-between items-center sm:flex w-auto order-1">
                        <ul class="flex font-medium flex-row space-x-8 mt-0">
                            <li>
                                <Link :href="route('home')" preserve-state
                                      class="block  rounded bg-transparent text-base-700 p-0 dark:text-white"
                                      aria-current="page">
                                    Home
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('team')" preserve-state
                                      class="block   rounded bg-transparent text-base-700 p-0 dark:text-white">
                                    Team
                                </Link>
                            </li>
                            <li>
                                <a :href="'https://github.com/ahmed-fawzy99/my-repo'" target="_blank"
                                   class="block   rounded bg-transparent text-base-700 p-0 dark:text-white">
                                    GitHub
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <div>
            <slot/>
        </div>

        <footer class="p-4 bg-base-100 dark:bg-base-800 mt-10 md:mt-0 pb-auto flex-grow">
            <div class="mx-auto max-w-screen-xl text-center">
                <ul class="flex flex-wrap justify-center items-center mb-2 text-gray-900 dark:text-white">
                    <li>
                        <Link :href="route('home')" preserve-state class="mr-4 hover:underline md:mr-6 ">Home</Link>
                    </li>
                    <li>
                        <Link :href="route('team')"  preserve-state class="mr-4 hover:underline md:mr-6">Team</Link>
                    </li>
                    <li>
                        <a href="https://github.com/ahmed-fawzy99/my-repo" class="mr-4 hover:underline md:mr-6 ">GitHub</a>
                    </li>
                    <li>
                        <button
                            @click="toggleDark()"
                            class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700
                                    focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700
                                    rounded-lg text-sm p-2.5 "
                        >
                            <svg
                                id="theme-toggle-dark-icon"
                                class="w-5 h-5"
                                :class="isDark ? 'block' : 'hidden'"
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
                                :class="isDark ? 'hidden' : 'block'"
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
                    </li>
                </ul>

                <p class="text-gray-500 dark:text-gray-400">My Repo - Open-source Secure File-Sharing Solution.</p>
            </div>
        </footer>

    </div>
</template>
