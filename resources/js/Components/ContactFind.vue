<script setup>
import {initDropdowns} from "flowbite";
import {onMounted} from "vue";
import {Link} from '@inertiajs/vue3';
import {contactRequestResponse, sendContactRequest} from "@/js-helpers/contacts-helper.js";

defineProps({
    id: String
})
const selectedContacts = defineModel();

onMounted(() => {
    initDropdowns();
});
</script>

<template>

    <button :id="id+'Button'" :data-dropdown-toggle="id" data-dropdown-placement="bottom" class="text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800" type="button">
        <slot name="button" />
    </button>

    <!-- Dropdown menu -->
    <div :id="id" class="z-10 hidden bg-white rounded-lg shadow w-60 dark:bg-base-700">
        <div class="p-3">
            <label for="input-group-search" class="sr-only">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-base-500 dark:text-base-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <slot name="search"/>
            </div>
        </div>
        <ul class="" :aria-labelledby="id+'Button'">
            <li class="h-56 py-2 overflow-y-auto text-base-700 dark:text-base-200">
              <slot name="contact"/>
            </li>
            <li v-if="selectedContacts.length" class="min-h-8 border-t border-base-500 text-sm p-2">
                <h3 class="font-bold">Selected Contacts:</h3>
                <span  v-for="selectedContact in selectedContacts">{{selectedContact.name}}, </span>
            </li>
        </ul>
        <slot name="action"/>

    </div>

</template>
