<script setup>
import {Head, Link} from '@inertiajs/vue3';
import LandingLayout from "@/Layouts/LandingLayout.vue";
import 'primeicons/primeicons.css'
</script>

<template>
    <Head title="Welcome"/>
    <LandingLayout>
            <section class="bg-white dark:bg-gray-900">
                <div
                    class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-8 grid-cols-12 min-h-screen-[4rem]">
                    <div class="mr-auto lg:place-self-center col-span-12 md:col-span-7">
                        <h1 class="mb-4 text-center md:text-left text-4xl font-extrabold tracking-tight leading-none md:text-5xl
                        xl:text-6xl dark:text-white animate-slidein [--slidein-delay:50ms] opacity-0">
                            <span class="underline italic decoration-primary-700 mr-1">TRUE</span> Privacy... <br> As It
                            Should Be.
                        </h1>
                        <div class="md:hidden animate-slidein [--slidein-delay:100ms] opacity-0">
                            <img src="storage/img/landing_page_image.webp" alt="mockup">
                        </div>
                        <p class="max-w-2xl text-center md:text-left mb-6 font-light text-gray-500 lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400 animate-slidein [--slidein-delay:150ms] opacity-0">
                            MyRepo is What you looking for with our highly secure system where
                            you can save all your files safely.
                        </p>
                        <div class="flex gap-2 justify-center md:justify-start animate-slidein [--slidein-delay:500ms] opacity-0">
                            <Link :href="route('register')"
                                  class="inline-flex items-center justify-center px-5 py-3 mr-3 text-base font-medium text-center text-white rounded-lg bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-900">
                                Get started
                                <span class="pi pi-arrow-right ml-2"/>
                            </Link>
                            <a href="https://github.com/ahmed-fawzy99/my-repo" target="_blank"
                               class="inline-flex items-center justify-center px-5 py-3 text-base font-medium text-center text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:text-white dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                                Source Code
                                <span class="pi pi-github ml-2"/>
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:mt-0 md:col-span-5 md:flex md:place-self-center md:aspect-square animate-slidein [--slidein-delay:100ms] opacity-0">
                        <img src="storage/img/landing_page_image.webp" alt="mockup">
                    </div>
                </div>
            </section>
    </LandingLayout>

</template>
