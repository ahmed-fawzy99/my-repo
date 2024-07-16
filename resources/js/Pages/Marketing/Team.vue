<script setup>
import { Head, Link } from '@inertiajs/vue3';
import LandingLayout from "@/Layouts/LandingLayout.vue";
import 'primeicons/primeicons.css'

</script>

<template>
    <Head title="Welcome" />
    <LandingLayout>
        <section class="bg-white dark:bg-base-900">
            <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
                <!-- Intro -->
                <div class="mx-auto max-w-screen-sm text-center mb-8 lg:mb-16  animate-slidein [--slidein-delay:50ms] opacity-0">
                    <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-base-900 dark:text-white">
                        Our Team
                    </h2>
                    <p class="font-light text-base-500 lg:mb-16 sm:text-xl dark:text-base-400">
                        MyRepo cloud was developed Ahmed and Marco. This is the cumulative work for our graduation project for
                        faculty of Engineering, Ain Shams University, under the supervision of Prof. Sherin Moussa.
                    </p>
                </div>

                <div class="grid gap-8 mb-6 lg:mb-16 md:grid-cols-2">
                    <!-- Ahmed -->
                    <div class="items-center bg-base-50 rounded-lg shadow sm:flex dark:bg-base-800 dark:border-base-700 animate-slidein [--slidein-delay:100ms] opacity-0">
                        <a href="https://github.com/ahmed-fawzy99" target="_blank">
                            <img width="500" height="500"  class=" rounded-lg sm:rounded-none sm:rounded-l-lg" src="storage/img/ahmed.webp" alt="Ahmed Avatar">
                        </a>
                        <div class="p-5">
                            <h3 class="text-xl font-bold tracking-tight text-base-900 dark:text-white text-center sm:text-left">
                                <a href="https://github.com/ahmed-fawzy99" target="_blank" >Ahmed Deghady</a>
                            </h3>
                            <p class="text-base-500 dark:text-base-400 text-center sm:text-left">Full-Stack Developer</p>
                            <p class="mt-3 mb-4 font-light text-base-500 dark:text-base-400 text-center sm:text-left">Passionate about networking, back-end development, and (sometimes) front-end.</p>
                            <ul class="flex space-x-4 sm:mt-0">
                                <li class="mx-auto sm:mx-0">
                                    <a href="https://github.com/ahmed-fawzy99" class="text-base-500 hover:text-base-900 dark:hover:text-white">
                                        <span class="pi pi-github ml-2"/>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Marco -->
                    <div class="items-center bg-base-50 rounded-lg shadow sm:flex dark:bg-base-800 dark:border-base-700 animate-slidein [--slidein-delay:250ms] opacity-0">
                        <a href="https://github.com/SuperShjt" target="_blank">
                            <img width="500" height="500" class=" rounded-lg sm:rounded-none sm:rounded-l-lg" src="storage/img/marco.webp" alt="Marco Avatar">
                        </a>
                        <div class="p-5">
                            <h3 class="text-xl font-bold tracking-tight text-base-900 dark:text-white text-center sm:text-left">
                                <a href="https://github.com/ahmed-fawzy99" target="_blank" >Marco Kamal</a>
                            </h3>
                            <p class="text-base-500 dark:text-base-400 text-center sm:text-left">Back-End Developer</p>
                            <p class="mt-3 mb-4 font-light text-base-500 dark:text-base-400 text-center sm:text-left">Passionate about back-end development, cloud solutions and designing networks.</p>
                            <ul class="flex space-x-4 sm:mt-0">
                                <li class="mx-auto sm:mx-0">
                                    <a href="https://github.com/ahmed-fawzy99" class="text-base-500 hover:text-base-900 dark:hover:text-white">
                                        <span class="pi pi-github ml-2"/>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </LandingLayout>

</template>
