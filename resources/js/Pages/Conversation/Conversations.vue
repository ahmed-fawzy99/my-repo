<script setup>
import {Head, Link} from '@inertiajs/vue3';
import {onMounted, ref, useAttrs, watch} from "vue";
import {decryptConversation, sendMessage, validatePrivateKey} from "@/js-helpers/chat-helpers.js";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConversationTabs from "@/Components/Tabs/ConversationTabs.vue";
import ChatBubble from "@/Components/ChatBubble.vue";
import TextInput from "@/Components/TextInput.vue";
import ToolTip from "@/Components/ToolTip.vue";
import {toaster} from "@/js-helpers/generic-helpers.js";

const props = defineProps({
    conversations: Object,
});

const activeConversation = ref(props.conversations.data[3].id);
const secretKey = ref('announce pill allow truck attract rhythm click limit duty pass devote despair');
const messageInput = ref('');
const conversationMessagesCount = ref(0);
const attrs = useAttrs()

const setActiveConversation = (conversationId) => {
    activeConversation.value = conversationId;
}
const getSenderName = (message) => {
    const conversation = props.conversations.data.filter(convo => convo.id === activeConversation.value)[0];
    return message.sender_id === conversation.user_1.id ? conversation.user_1.name : conversation.user_2.name;
}
const getOtherParty = () => {
    const conversation = props.conversations.data.filter(convo => convo.id === activeConversation.value)[0];
    return conversation.user_1.id === attrs.auth.user.id ? conversation.user_2 : conversation.user_1;
}
const send = async (message, conversationId, senderPrvMnemonic, senderPubKey, receiverPubKey) => {
    if (!receiverPubKey){
        toaster('error', 'Receiver public key not found');
        messageInput.value = '';
        return;
    }
    const isCorrectPrv = await validatePrivateKey(senderPrvMnemonic, senderPubKey, receiverPubKey);
    if (!isCorrectPrv) {
        toaster('error', 'Invalid Secret Key');
        return;
    }
    await sendMessage(message, conversationId, senderPrvMnemonic, receiverPubKey);
    messageInput.value = '';
    const chatContent = document.getElementById('chat-content');
    if (conversationMessagesCount.value) {
        chatContent.scrollBy({
            top: chatContent.scrollHeight,
            behavior: 'smooth'
        });
    }
    countConversationMessages();
}
const countConversationMessages = () => {
    conversationMessagesCount.value = props.conversations.data.filter(convo => convo.id === activeConversation.value)[0].messages.length;
    return conversationMessagesCount.value;
};
watch(activeConversation, countConversationMessages);
onMounted(() => {
    countConversationMessages();

    document.getElementById('chat').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            if (messageInput.value) {
                send(messageInput.value, activeConversation.value, secretKey.value, attrs.auth.user.public_key, getOtherParty().public_key);
            }
        }
    });
    decryptConversation(props.conversations.data[3], secretKey.value, getOtherParty().public_key);

});


</script>

<template>
    <Head title="My Conversations"/>
    <AuthenticatedLayout freeContent>
        <template #tabs>
            <ConversationTabs/>
        </template>
        <template #free-content>
            <div
                class="w-full text-white bg-primary-700 dark:bg-primary-950 text-sm p-1 flex items-center justify-between px-4 gap-4">
                <span class="w-1/3">Paste Your Private Secret Word: <ToolTip>This key will be used to encrypt your outgoing messages <br> and decrypt your conversations.</ToolTip></span>
                <TextInput v-model="secretKey" type="text" class="w-full !p-1.5"
                           placeholder="Enter your secret word"/>
            </div>
            <p class="">{{conversations.data[3]}}</p>

            <div class="h-[calc(100vh-4rem)] w-full grid grid-cols-12 dark:bg-base-950">

                <!-- Conversation Selector -->
                <div
                    class="col-span-3 bg-base-200 dark:bg-base-800 shadow-sm border-r border-base-200 dark:border-base-900">
                    <div class="px-4 py-1.5">
                        <label for="simple-search" class="sr-only">Search</label>
                        <div class="relative w-full">
                            <span></span>
                            <input type="text" id="simple-search" class="bg-base-50 border border-base-300 text-base-900 text-sm rounded-lg
                        focus:ring-primary-500 focus:border-primary-500 block w-full ps-10
                        dark:bg-base-700 dark:border-base-600 dark:placeholder-base-400 dark:text-white
                        dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                   placeholder="Search branch name..." required/>
                        </div>
                    </div>
                    <ul class=" ">
                        <li v-for="(conversation, index) in conversations.data" :key="conversation.id"
                            @click="setActiveConversation(conversation.id)"
                            class="p-3 sm:py-4 hover:bg-base-300 dark:hover:bg-base-700 cursor-pointer transition-colors duration-200"
                            :class="{
                        'bg-base-100 dark:bg-base-800': index%2===0,
                        'bg-base-200 dark:bg-base-900': index%2===1,
                    }"
                        >
                            <div class="flex items-center space-x-4">
                                <span class="pi pi-user text-2xl mr-2"/>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-base-900 truncate dark:text-white">
                                        {{
                                            conversation.user_1.id === $attrs.auth.user.id ? conversation.user_2.name : conversation.user_1.name
                                        }}
                                    </p>
                                    <p class="text-sm text-base-500 truncate dark:text-base-400">
                                        {{
                                            conversation.user_1.id === $attrs.auth.user.id ? conversation.user_2.email : conversation.user_1.email
                                        }}
                                    </p>
                                </div>
                            </div>
                        </li>
                    </ul>

                </div>

                <!-- Chat Screen -->
                <div class="col-span-9  overflow-y-scroll " id="chat-content">
                    <div class="sticky top-0 w-full p-3 bg-base-200 dark:bg-base-900 flex justify-between items-center">
                        <div class="flex items-center">
                            <span class="pi pi-user text-lg text-black rounded-full p-1 shadow-sm bg-white mr-2"/>
                            <p class="text-sm">{{ getOtherParty().name }}</p>
                        </div>
                        <span class="pi pi-trash me-4"/>
                    </div>

                    <div v-if="conversationMessagesCount" class="p-4 min-h-full">
                        <ChatBubble
                            :id="Math.random().toString(36).slice(2, 5)"
                            v-for="message in conversations.data.filter(convo => convo.id === activeConversation)[0].messages"
                            class="mb-4"
                            :isSender="message.sender_id === $attrs.auth.user.id"
                            :msg="{
                        id: message.id,
                        sender_id: message.sender_id,
                        auth_id: $attrs.auth.user.id,
                        name: getSenderName(message),
                        content: message.content,
                        time: message.created_at,
                        read: message.is_read
                    }"
                        >
                        </ChatBubble>
                    </div>
                    <div v-else class="p-4 min-h-full flex flex-col justify-center items-center">
                        <span class="pi pi-inbox text-4xl text-base-500 mb-2"/>
                        <p class="text-base-500">No messages yet</p>
                    </div>

                    <div class="sticky bottom-16 px-4">
                        <label for="chat" class="sr-only">Your message</label>
                        <div class="flex items-center px-3 py-2 rounded-2xl bg-base-200 dark:bg-base-700 shadow-md">
                            <input id="chat" v-model="messageInput" type="text"
                                   class="block mr-2 p-2.5 w-full text-sm text-base-900 bg-white rounded-2xl
                                    border border-base-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-base-800 dark:border-base-600
                                    dark:placeholder-base-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                   placeholder="Your message..."/>
                            <Link type="submit" href="#"
                                  @click="send(messageInput, activeConversation, secretKey, $attrs.auth.user.public_key ,getOtherParty().public_key)"
                                  class="inline-flex justify-center p-2 text-primary-600 rounded-full
                                cursor-pointer hover:bg-primary-100 dark:text-primary-500 dark:hover:bg-base-600">

                                <span class="pi pi-send text-xl "/>
                                <!--                            <svg class="w-5 h-5 rotate-90 rtl:-rotate-90" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">-->
                                <!--                                <path d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z"/>-->
                                <!--                            </svg>-->
                                <span class="sr-only">Send message</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </template>


    </AuthenticatedLayout>
</template>
