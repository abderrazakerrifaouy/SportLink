<script setup>
import { ref, onMounted, computed, watch, nextTick } from 'vue';
import { useUserStore } from '@/stores/userStore';
import { useAuthStore } from '@/stores/AuthStore';
import { useChatStore } from '@/stores/messageStore';

const userStore = useUserStore();
const authStore = useAuthStore();
const chatStore = useChatStore();

const newMessage = ref('');
const messagesContainer = ref(null);
const selectedConversation = ref(null);

const sortedConversations = computed(() => {
  if (!userStore.conversations) return [];
  return [...userStore.conversations].sort((a, b) => {
    const timeA = new Date(a.messages[a.messages.length - 1]?.created_at || 0);
    const timeB = new Date(b.messages[b.messages.length - 1]?.created_at || 0);
    return timeB - timeA;
  });
});

const messageStore = computed(() => ({
  conversations: userStore.conversations,
  selectedConversation: selectedConversation.value,
  currentMessages: userStore.messages
}));

const selectConversation = async (conv) => {
  selectedConversation.value = conv;
  await userStore.fetchMessages(authStore.user.id, conv.user.id);
  scrollToBottom();
};

const sendMessage = async () => {
  if (!newMessage.value.trim() || !selectedConversation.value) return;
  try {
    await userStore.sendMessage(selectedConversation.value.user.id, newMessage.value);
    newMessage.value = '';
    scrollToBottom();
    await userStore.fetchConversations();
  } catch (error) {
    console.error("Error sending message:", error);
  }
};

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
  });
};

const formatTime = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

onMounted(async () => {
  await userStore.fetchConversations();
  if (authStore.user?.id) {
    chatStore.startListening(authStore.user.id);
  }
});

watch(() => userStore.messages.length, () => {
  scrollToBottom();
});

watch(() => authStore.user?.id, (newId) => {
  if (newId) chatStore.startListening(newId);
});
</script>

<template>
  <div class="flex h-[calc(100vh-200px)] bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="w-1/3 border-r border-gray-100 flex flex-col bg-gray-50/50">
      <div class="p-4 border-b border-gray-100 bg-white">
        <h1 class="text-xl font-bold text-gray-900">Messages</h1>
      </div>
      <div class="flex-1 overflow-y-auto">
        <div v-if="messageStore.conversations.length === 0" class="p-8 text-center text-gray-500">
          No conversations
        </div>
        <div
          v-for="conv in sortedConversations"
          :key="conv.user.id"
          @click="selectConversation(conv)"
          :class="['p-4 hover:bg-white cursor-pointer transition flex items-center gap-4 border-b border-gray-50',
          messageStore.selectedConversation?.user?.id === conv.user.id ? 'bg-white border-l-4 border-l-blue-600' : 'bg-white']"
        >
          <img :src="conv.user?.profileImage || '/default-avatar.png'" class="w-12 h-12 rounded-full object-cover shadow-sm">
          <div class="flex-1 min-w-0">
            <div class="flex justify-between items-baseline">
              <p class="font-bold text-gray-900 truncate">{{ conv.user.name }}</p>
              <span class="text-[10px] text-gray-400">{{ formatTime(conv.messages[conv.messages.length - 1]?.created_at) }}</span>
            </div>
            <p class="text-sm text-gray-500 truncate">
              {{ conv.messages[conv.messages.length - 1]?.message || 'No messages yet' }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="flex-1 flex flex-col bg-white">
      <template v-if="messageStore.selectedConversation">
        <div class="p-4 border-b border-gray-100 flex items-center gap-3 bg-white shadow-sm">
          <img :src="messageStore.selectedConversation.user?.profileImage || '/default-avatar.png'" class="w-10 h-10 rounded-full object-cover">
          <div><p class="font-bold text-gray-900">{{ messageStore.selectedConversation.user.name }}</p></div>
        </div>
        <div class="flex-1 overflow-y-auto p-6 bg-[#f0f2f5] flex flex-col gap-3" ref="messagesContainer">
          <div
            v-for="msg in messageStore.currentMessages"
            :key="msg.id"
            :class="['max-w-[70%] p-3 rounded-2xl text-sm shadow-sm',
            msg.sender_id === authStore.user?.id ? 'bg-blue-600 text-white self-end rounded-tr-none' : 'bg-white text-gray-800 self-start rounded-tl-none']"
          >
            {{ msg.message }}
          </div>
        </div>
        <div class="p-4 bg-gray-50 border-t border-gray-100">
          <div class="flex gap-2 bg-white rounded-xl p-2 border border-gray-200 shadow-sm">
            <input type="text" v-model="newMessage" @keyup.enter="sendMessage" placeholder="Type a message..." class="flex-1 bg-transparent outline-none px-2 py-1 text-sm">
            <button @click="sendMessage" :disabled="!newMessage.trim()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition disabled:opacity-50">Send</button>
          </div>
        </div>
      </template>
      <div v-else class="flex-1 flex flex-col items-center justify-center text-gray-400 bg-gray-50">
        <p class="font-medium">Choose a friend to chat</p>
      </div>
    </div>
  </div>
</template>
