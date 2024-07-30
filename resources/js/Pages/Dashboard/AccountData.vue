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
        <div class="flex flex-col md:flex-row justify-between items-center mb-4">
            <h1 class="text-4xl mb-4">Account Data</h1>
            <div class="flex gap-4">
                <PrimaryButton @click="router.get(route('profile.edit', {id: $attrs.auth.user.id}))">
                    <div class="flex items-center">
                        <span class="pi pi-user-edit text-xl mr-2" /> <span>Edit Your Data</span>
                    </div>
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
                    <DT>Public Key 1
                        <ToolTip direction="top">
                            This is your ECDH public key, which is used for key exchange with other users.
                        </ToolTip>
                    </DT>
                    <DD>{{ $attrs.auth.user.public_key_ecdh }}</DD>
                </DescriptionListItem>
                <DescriptionListItem>
                    <DT>Public Key 2
                        <ToolTip direction="top">
                            This is your EdDSA public key, which is used for signing and verifying messages.
                        </ToolTip>
                    </DT>
                    <DD>{{ $attrs.auth.user.public_key_eddsa }}</DD>
                </DescriptionListItem>
            </DescriptionList>

        </Card>
    </AuthenticatedLayout>
</template>
