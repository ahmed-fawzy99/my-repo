<script setup>
import {Link} from '@inertiajs/vue3';
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
                        <Link :href="route('login')"
                              class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 lg:px-5 py-2 lg:py-2.5 mr-2 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">
                            Log in
                        </Link>
                        <Link
                            v-if="$page.props.auth.user"
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
                        <a href="https://github.com" class="mr-4 hover:underline md:mr-6 ">GitHub</a>
                    </li>
                </ul>
                <p class="text-gray-500 dark:text-gray-400">My Repo - Open-source Secure File-Sharing Solution.</p>
            </div>
        </footer>

    </div>
</template>
