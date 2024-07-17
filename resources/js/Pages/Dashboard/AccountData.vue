<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import Card from "@/Components/Card.vue";

import DescriptionListItem from "@/Components/DescriptionList/DescriptionListItem.vue";
import DescriptionList from "@/Components/DescriptionList/DescriptionList.vue";
import DT from "@/Components/DescriptionList/DT.vue";
import DD from "@/Components/DescriptionList/DD.vue";
import ToolTip from "@/Components/ToolTip.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DashboardTabs from "@/Components/Tabs/DashboardTabs.vue";


</script>

<template>
    <Head title="Account Data"/>

    <AuthenticatedLayout>
        <template #tabs>
            <DashboardTabs />
        </template>
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-4xl mb-4">Account Data</h1>
            <div class="flex inline-flex gap-4">
                <PrimaryButton @click="router.get(route('profile.edit', {id: $attrs.auth.user.id}))">
                    <span class="pi pi-user-edit text-xl mr-2" /> Edit Your Data
                </PrimaryButton>
            </div>
        </div>

        <Card>
            <DescriptionList>
                <DescriptionListItem>
                    <DT>Name</DT>
                    <DD>{{ $attrs.auth.user.name }}</DD>
                </DescriptionListItem>
                <DescriptionListItem>
                    <DT>ULID</DT>
                    <DD>{{ $attrs.auth.user.id }}</DD>
                </DescriptionListItem>
                <DescriptionListItem colored>
                    <DT>Email Address</DT>
                    <DD>{{ $attrs.auth.user.email }}</DD>
                </DescriptionListItem>
                <DescriptionListItem colored>
                    <DT>Email Verification Status</DT>
                    <DD>{{ $attrs.auth.user.email_verified_at ? 'Verified ✅' : 'Unverified ❌' }}</DD>
                </DescriptionListItem>
                <DescriptionListItem>
                    <DT>Public Key
                        <ToolTip direction="right">
                            This is your public key. FURTHER EXPLAINATION TO BE PUT HERE
                        </ToolTip>
                    </DT>
                    <DD>{{ $attrs.auth.user.public_key }}</DD>
                </DescriptionListItem>
                <DescriptionListItem></DescriptionListItem>
            </DescriptionList>

        </Card>
    </AuthenticatedLayout>
</template>
