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
  <div class="flex h-[calc(100vh-140px)] bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="w-1/3 border-r border-gray-100 flex flex-col bg-gray-50/30">
      <div class="p-6 border-b border-gray-100 bg-white">
        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Messages</h1>
      </div>

      <div class="flex-1 overflow-y-auto custom-scrollbar">
        <div v-if="messageStore.conversations.length === 0" class="p-12 text-center">
          <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 12h.01M12 10h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
          </div>
          <p class="text-gray-400 font-bold text-sm uppercase tracking-widest">No chats found</p>
        </div>

        <div
          v-for="conv in sortedConversations"
          :key="conv.user.id"
          @click="selectConversation(conv)"
          :class="[
            'p-4 cursor-pointer transition-all duration-200 flex items-center gap-4 relative border-b border-gray-50/50',
            messageStore.selectedConversation?.user?.id === conv.user.id
              ? 'bg-white shadow-sm z-10'
              : 'hover:bg-indigo-50/30'
          ]"
        >
          <div v-if="messageStore.selectedConversation?.user?.id === conv.user.id" class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-600 rounded-r-full"></div>

          <div class="relative shrink-0">
            <img :src="conv.user?.profileImage || '/default-avatar.png'" class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm">
            <div v-if="conv.user?.isActive" class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
          </div>

          <div class="flex-1 min-w-0">
            <div class="flex justify-between items-baseline mb-0.5">
              <p class="font-black text-gray-900 truncate tracking-tight text-[15px]">{{ conv.user.name }}</p>
              <span class="text-[10px] font-bold text-gray-400 uppercase">{{ formatTime(conv.messages[conv.messages.length - 1]?.created_at) }}</span>
            </div>
            <p class="text-xs font-medium text-gray-500 truncate leading-relaxed">
              <span v-if="conv.messages[conv.messages.length - 1]?.sender_id === authStore.user?.id" class="text-indigo-500 font-bold">You:</span>
              {{ conv.messages[conv.messages.length - 1]?.message || 'No messages yet' }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="flex-1 flex flex-col bg-white">
      <template v-if="messageStore.selectedConversation">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-white/80 backdrop-blur-md sticky top-0 z-20">
          <div class="flex items-center gap-3 cursor-pointer" @click="goToProfile(messageStore.selectedConversation.user.id)">
            <img :src="messageStore.selectedConversation.user?.profileImage || '/default-avatar.png'" class="w-10 h-10 rounded-full object-cover border border-gray-100">
            <div>
              <p class="font-black text-gray-900 tracking-tight leading-none">{{ messageStore.selectedConversation.user.name }}</p>
              <span class="text-[10px] font-bold text-green-500 uppercase tracking-widest mt-1 inline-block">Online</span>
            </div>
          </div>
          <button class="p-2 text-gray-400 hover:text-indigo-600 transition hover:bg-indigo-50 rounded-xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
          </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 bg-gray-50/50 flex flex-col gap-4 custom-scrollbar" ref="messagesContainer">
          <div
            v-for="(msg, index) in messageStore.currentMessages"
            :key="msg.id"
            :class="[
              'max-w-[75%] p-3 px-4 text-sm font-medium shadow-sm leading-relaxed',
              msg.sender_id === authStore.user?.id
                ? 'bg-indigo-600 text-white self-end rounded-2xl rounded-tr-none'
                : 'bg-white text-gray-800 self-start rounded-2xl rounded-tl-none border border-gray-100'
            ]"
          >
            {{ msg.message }}
            <div :class="['text-[9px] mt-1 opacity-70 font-bold uppercase', msg.sender_id === authStore.user?.id ? 'text-right' : 'text-left']">
              {{ formatTime(msg.created_at) }}
            </div>
          </div>
        </div>

        <div class="p-4 bg-white border-t border-gray-100">
          <div class="flex gap-3 bg-gray-50 rounded-2xl p-2 border border-gray-100 focus-within:ring-2 focus-within:ring-indigo-100 transition-all">
            <input
              type="text"
              v-model="newMessage"
              @keyup.enter="sendMessage"
              placeholder="Write a message..."
              class="flex-1 bg-transparent outline-none px-4 py-2 text-sm font-medium text-gray-700 placeholder:text-gray-400"
            >
            <button
              @click="sendMessage"
              :disabled="!newMessage.trim()"
              class="bg-indigo-600 text-white p-2.5 rounded-xl font-bold hover:bg-indigo-700 transition-all active:scale-95 disabled:opacity-30 shadow-md shadow-indigo-100"
            >
              <svg class="w-5 h-5 transform rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
            </button>
          </div>
        </div>
      </template>

      <div v-else class="flex-1 flex flex-col items-center justify-center text-center bg-gray-50/30 p-12">
        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 mb-6">
          <svg class="w-12 h-12 text-indigo-100" fill="currentColor" viewBox="0 0 20 20"><path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/><path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/></svg>
        </div>
        <h3 class="text-xl font-black text-gray-900 tracking-tight">Select a conversation</h3>
        <p class="text-gray-400 text-sm font-medium mt-2 max-w-[240px]">Choose one of your friends from the list to start chatting.</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}
</style>
