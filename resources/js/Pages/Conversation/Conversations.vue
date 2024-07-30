<script setup>
import {Head, router} from '@inertiajs/vue3';
import {onMounted, ref, useAttrs, watch} from "vue";
import {
    decryptConversation,
    decryptMessage,
    deleteMessage,
    sendMessage,
    validatePrivateKey
} from "@/js-helpers/chat-helpers.js";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ConversationTabs from "@/Components/Tabs/ConversationTabs.vue";
import ChatBubble from "@/Components/ChatBubble.vue";
import TextInput from "@/Components/TextInput.vue";
import ToolTip from "@/Components/ToolTip.vue";
import {toaster} from "@/js-helpers/generic-helpers.js";
import debounce from "lodash/debounce.js";

const props = defineProps({
    conversations_enc: Object,
    passedConversationId: {
        type: String,
        default: null
    },
});
const conversations = {...props.conversations_enc};

const attrs = useAttrs()
const activeConversationId = ref(props.passedConversationId ?? (conversations.data[0]?.id ?? null));
const secretKey = ref('');
const messageInput = ref('');
const contactSearch = ref('');
const conversationMessagesCount = ref(0);
const isValidKey = ref(false);
const audio = new Audio('storage/sounds/incoming-msg.mp3');

const getActiveConversation = () => {
    return activeConversationId ? conversations.data.filter(convo => convo.id === activeConversationId.value)[0] : null;
}
const getActiveConversationEnc = () => {
    return activeConversationId ? props.conversations_enc.data.filter(convo => convo.id === activeConversationId.value)[0] : null;
}
const setActiveConversation = (conversationId) => {
    activeConversationId.value = conversationId;
}

const getConversationById = (conversationId) => {
    return conversations.data.filter(convo => convo.id === conversationId)[0];
}
const getSenderName = (message) => {
    const conversation = getActiveConversation();
    return message.sender_id === conversation.user_1.id ? conversation.user_1.name : conversation.user_2.name;
}
const getOtherParty = () => {
    const conversation = getActiveConversation();
    if (!conversation) {
        return null;
    }
    return conversation.user_1.id === attrs.auth.user.id ? conversation.user_2 : conversation.user_1;
}

const toggleSecretKey = () => {
    const secretKeyInput = document.getElementById('secret-key');
    if (secretKeyInput.type === 'password') {
        secretKeyInput.type = 'text';
    } else {
        secretKeyInput.type = 'password';
    }
}
const send = async (message, conversationId, senderPrvMnemonic, senderPubKey, receiverPubKey) => {
    if (!message) { // check if message is empty
        toaster('error', 'Message cannot be empty');
        return;
    }
    if (receiverPubKey.some(el => !el)) { // check if any of the keys is empty
        toaster('error', 'Receiver public key not found. Please ask them to finalize their registeration');
        messageInput.value = '';
        return;
    }
    const isCorrectPrv = await validatePrivateKey(senderPrvMnemonic, senderPubKey);
    if (!isCorrectPrv) { // check if secret key is invalid
        toaster('error', 'Invalid Secret Key');
        return;
    }
    await sendMessage(message, conversationId, senderPrvMnemonic, receiverPubKey);

    const messages = getActiveConversation().messages;
    messages.push(getActiveConversationEnc().messages[getActiveConversationEnc().messages.length - 1]);
    messages[getActiveConversation().messages.length - 1].content = message;


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

const deleteMsg = async (msgId) => {
    const isCorrectPrv = await validatePrivateKey(secretKey.value, [attrs.auth.user.public_key_ecdh, attrs.auth.user.public_key_eddsa]);
    if (!isCorrectPrv) { // check if secret key is invalid
        toaster('error', 'Invalid Secret Key');
        return;
    }
    await deleteMessage(msgId);
    getActiveConversation().messages.filter(msg => msg.id === msgId)[0].content = '';
    sortMsgs(getActiveConversation().messages);
}
const countConversationMessages = () => {
    if (!getActiveConversation() || !getActiveConversation().messages) {
        return 0;
    }
    conversationMessagesCount.value = getActiveConversation().messages.length;
    return conversationMessagesCount.value;
};
const sortMsgs = (msgs) => {
    return msgs.sort((a, b) => a.id - b.id);
}

onMounted(() => {
    if (countConversationMessages()) {
        sortMsgs(getActiveConversation().messages);
        decryptConversation(getActiveConversation(), secretKey.value, [getOtherParty().public_key_ecdh, getOtherParty().public_key_eddsa]);
    }
    document.getElementById('chat').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            if (messageInput.value) {
                send(messageInput.value, activeConversationId.value, secretKey.value, [attrs.auth.user.public_key_ecdh, attrs.auth.user.public_key_eddsa], [getOtherParty().public_key_ecdh, getOtherParty().public_key_eddsa]);
            }
        }
    });

});

const search = debounce(() => {
    activeConversationId.value = null;
    router.visit(route('conversations.index', {contactSearch: contactSearch.value}),
        {preserveState: true, preserveScroll: true});
}, 250);
watch(contactSearch, search);

watch(activeConversationId, async (newVal, oldVal) => {
    if (newVal !== oldVal) {
        if (countConversationMessages()) {
            router.patch(route('conversations.update', {id: newVal}), {id: newVal}, {
                preserveState: true,
                preserveScroll: true,
                onSuccess: async () => {
                    try {
                        await decryptConversation(getActiveConversation(), secretKey.value, [getOtherParty().public_key_ecdh, getOtherParty().public_key_eddsa]);
                    } catch (e) {
                        throw e;
                    }
                }
            });
        }
    }
});
watch(secretKey, debounce(async () => {
    if (secretKey.value.split(' ').length - 1 >= 11) { // for performance, only validate if the key is 12 words
        isValidKey.value = await validatePrivateKey(secretKey.value, [attrs.auth.user.public_key_ecdh, attrs.auth.user.public_key_eddsa]);
    }
    if (countConversationMessages() && isValidKey.value) {
        await decryptConversation(getActiveConversation(), secretKey.value, [getOtherParty().public_key_ecdh, getOtherParty().public_key_eddsa]);
    }
}, 250));

Echo.private(`messages.${attrs.auth.user.id}`)
    .listen('MessageSent', async (e) => {
        if (e.message.conversation.id === activeConversationId.value) {
            try {
                await decryptMessage(e.message, secretKey.value, [attrs.auth.user.public_key_ecdh, attrs.auth.user.public_key_eddsa], [getOtherParty().public_key_ecdh, getOtherParty().public_key_eddsa], false, 'auth.user');
            } catch (e) {
                throw e;
            } finally {
                getActiveConversation().messages.push(e.message);
                countConversationMessages();
                audio.play();
            }
        } else {
            getConversationById(e.message.conversation.id).messages.push(e.message);
        }
    })
    .listen('MessageDeleted', async (e) => {
        getActiveConversation().messages.filter(msg => msg.id === e.message.id)[0].content = '';
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
                class="w-full h-12 overflow-hidden text-white  bg-primary-700 dark:bg-primary-950 text-sm p-1 flex items-center justify-between px-4 gap-4">
                <span class="hidden md:block w-1/3">Paste Your Private Secret Word: <ToolTip>This key will be used to encrypt your outgoing messages <br> and decrypt your conversations.</ToolTip></span>
                <TextInput v-model="secretKey" type="text" autocomplete="off" class="w-full !p-1.5" id="secret-key"
                           :class="{ '!border-green-500 ': isValidKey, '!border-red-500': secretKey && !isValidKey }"
                           :disabled="getActiveConversation() === null"
                           placeholder="Enter your secret word"/>
                <span @click="toggleSecretKey"
                      class="pi pi-eye cursor-pointer"/>
            </div>

            <div class="h-[calc(100vh-10rem)] md:h-[calc(100vh-7rem)] w-full grid grid-cols-12 dark:bg-base-950 ">
                <!-- Conversation Selector -->
                <div
                    class="col-span-4 md:col-span-3 bg-base-200 dark:bg-base-800 shadow-sm border-r border-base-200 dark:border-base-900">
                    <div class="px-4 py-2">
                        <label for="conversation-search" class="sr-only">Search</label>
                        <div class="relative w-full">
                            <input type="text" id="conversation-search" v-model="contactSearch"
                                   class="bg-base-50 border border-base-300 text-base-900 text-sm rounded-lg
                        focus:ring-primary-500 focus:border-primary-500 block w-full
                        dark:bg-base-700 dark:border-base-600 dark:placeholder-base-400 dark:text-white
                        dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                   placeholder="Search for contact" required/>
                        </div>
                    </div>
                    <ul v-if="conversations.total">
                        <li v-for="(conversation, index) in conversations.data" :key="conversation.id"
                            @click="setActiveConversation(conversation.id)"
                            class="p-3 sm:py-4 hover:bg-base-300 dark:hover:bg-base-700 cursor-pointer "
                            :class="{
                        'bg-base-100 dark:bg-base-800': index%2===0,
                        'bg-base-200 dark:bg-base-900': index%2===1,
                    }"
                        >
                            <div class="flex items-center space-x-4">
                                <span class="hidden md:inline pi pi-user text-xs mr-0 md:text-2xl md:mr-2"/>
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
                    <div v-else class="p-4 flex flex-col justify-center items-center">
                        <span class="pi pi-inbox text-4xl text-base-500 mb-2"/>
                        <p class="text-base-500">No conversations yet</p>
                    </div>
                </div>

                <!-- Chat Screen -->
                <div class="col-span-8 md:col-span-9 overflow-y-scroll " id="chat-content">
                    <div
                        class="sticky h-12 top-0 w-full p-3 bg-base-200 dark:bg-base-900 flex justify-between items-center">
                        <div class="flex items-center">
                            <span class="pi pi-user text-lg text-black rounded-full p-1 shadow-sm bg-white mr-2"/>
                            <p class="text-sm">{{ conversations.total ? getOtherParty()?.name : null }}</p>
                        </div>

                        <!-- to be implemented later.. -->
                        <!-- <span class="pi pi-trash me-4"/>-->
                    </div>
                    <div v-if="conversationMessagesCount && activeConversationId" class="p-4  min-h-[calc(100%-7rem)]">
                        <ChatBubble
                            :id="Math.random().toString(36).slice(2, 5)"
                            v-for="message in getActiveConversation().messages"
                            :key="message.id"
                            class="mb-4"
                            :isSender="message.sender_id === $attrs.auth.user.id"
                            :msg="{
                                id: message.id,
                                sender_id: message.sender_id,
                                auth_id: $attrs.auth.user.id,
                                name: getSenderName(message),
                                content: message.content,
                                encrypted: message.encrypted,
                                invalid: message.invalid,
                                time: message.created_at,
                                read: message.is_read,
                                processing: getActiveConversation().processing
                            }"
                            @deleteMessage="deleteMsg"
                        >
                        </ChatBubble>
                    </div>
                    <div v-else class="p-4 flex flex-col justify-center items-center h-[calc(100%-7rem)]">
                        <span class="pi pi-inbox text-4xl text-base-500 mb-2"/>
                        <p class="text-base-500">No messages yet</p>
                    </div>

                    <div class="sticky bottom-2 px-4 ">
                        <label for="chat" class="sr-only">Your message</label>
                        <div class="flex items-center px-3 py-2 rounded-2xl bg-base-200 dark:bg-base-700 shadow-md">
                            <input id="chat" v-model="messageInput" type="text" required
                                   class="block mr-2 p-2.5 w-full text-sm text-base-900 bg-white rounded-2xl
                                    border border-base-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-base-800 dark:border-base-600
                                    dark:placeholder-base-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                   placeholder="Your message..."/>
                            <button type="button" href="#"
                                    @click="send(messageInput, activeConversationId, secretKey, [attrs.auth.user.public_key_ecdh, attrs.auth.user.public_key_eddsa], [getOtherParty().public_key_ecdh, getOtherParty().public_key_eddsa])"
                                    class="inline-flex justify-center p-2 text-primary-600 rounded-full
                                cursor-pointer hover:bg-primary-100 dark:text-primary-500 dark:hover:bg-base-600">

                                <span class="pi pi-send text-xl "/>
                                <!--                            <svg class="w-5 h-5 rotate-90 rtl:-rotate-90" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">-->
                                <!--                                <path d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z"/>-->
                                <!--                            </svg>-->
                                <span class="sr-only">Send message</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>


    </AuthenticatedLayout>
</template>
