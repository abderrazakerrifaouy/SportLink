<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/AuthStore'
import { useUserStore } from '@/stores/userStore'
import { storeToRefs } from 'pinia'
import MainLayout from '@/layouts/MainLayout.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const userStore = useUserStore()
const { user: currentUser } = storeToRefs(authStore)

const conversations = ref([])
const messages = ref([])
const selectedUser = ref(null)
const newMessage = ref('')
const loadingConversations = ref(false)
const loadingMessages = ref(false)
const sendingMessage = ref(false)
const error = ref('')

// Receive userId from route param if navigating directly to a conversation
const directUserId = computed(() => route.params.userId ? Number(route.params.userId) : null)

const avatarUrl = (name, img) =>
  img || `https://ui-avatars.com/api/?name=${encodeURIComponent(name || 'U')}&bg=064E3B&color=fff`

const loadConversations = async () => {
  loadingConversations.value = true
  try {
    conversations.value = await userStore.fetchConversations()
  } catch (err) {
    console.error('Failed to load conversations:', err)
  } finally {
    loadingConversations.value = false
  }
}

const selectConversation = async (otherUser) => {
  selectedUser.value = otherUser
  loadingMessages.value = true
  try {
    messages.value = await userStore.fetchMessages(currentUser.value.id, otherUser.id)
    router.replace(`/messages/${otherUser.id}`)
  } catch (err) {
    console.error('Failed to load messages:', err)
  } finally {
    loadingMessages.value = false
  }
}

const sendMessage = async () => {
  if (!newMessage.value.trim() || !selectedUser.value) return
  sendingMessage.value = true
  try {
    await userStore.sendMessage(selectedUser.value.id, newMessage.value.trim())
    newMessage.value = ''
  } catch (err) {
    error.value = 'Impossible d\'envoyer le message.'
    console.error(err)
  } finally {
    sendingMessage.value = false
  }
}

const deleteMessage = async (messageId) => {
  if (!confirm('Supprimer ce message ?')) return
  try {
    await userStore.deleteMessage(messageId)
  } catch (err) {
    console.error('Failed to delete message:', err)
  }
}

const formatTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })
}

watch(directUserId, async (id) => {
  if (id && conversations.value.length > 0) {
    const conv = conversations.value.find((c) => c.other_user?.id === id)
    if (conv) selectConversation(conv.other_user)
  }
})

onMounted(async () => {
  await loadConversations()
  if (directUserId.value) {
    // Find the conversation or load messages directly
    const conv = conversations.value.find((c) => c.other_user?.id === directUserId.value)
    if (conv) {
      await selectConversation(conv.other_user)
    } else {
      // Try to load user info and start a new conversation
      try {
        const userData = await userStore.fetchUser(directUserId.value)
        selectedUser.value = userData
        messages.value = await userStore.fetchMessages(currentUser.value.id, directUserId.value)
      } catch (err) {
        console.error('Failed to load user for messages:', err)
      }
    }
  }
})
</script>

<template>
  <MainLayout>
    <div class="flex gap-5 h-[calc(100vh-160px)]">
      <!-- Conversations list -->
      <div class="w-80 shrink-0 bg-white rounded-3xl border border-gray-100 shadow-sm flex flex-col overflow-hidden">
        <div class="p-5 border-b border-gray-50">
          <h2 class="font-black text-gray-900 text-lg">
            <i class="fa-regular fa-envelope text-emerald-600 mr-2"></i>Messages
          </h2>
        </div>

        <div class="flex-1 overflow-y-auto">
          <div v-if="loadingConversations" class="p-4 space-y-3">
            <div v-for="n in 5" :key="n" class="flex gap-3 animate-pulse">
              <div class="h-12 w-12 bg-gray-200 rounded-2xl shrink-0"></div>
              <div class="flex-1 space-y-2 pt-1">
                <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                <div class="h-3 bg-gray-100 rounded w-1/2"></div>
              </div>
            </div>
          </div>

          <div v-else-if="conversations.length === 0" class="flex flex-col items-center justify-center h-40 text-center p-4">
            <i class="fa-regular fa-comment-dots text-gray-300 text-4xl mb-2"></i>
            <p class="text-sm text-gray-400">Aucune conversation</p>
          </div>

          <div v-else>
            <button
              v-for="conv in conversations"
              :key="conv.id || conv.other_user?.id"
              @click="selectConversation(conv.other_user)"
              :class="[
                'w-full flex items-center gap-3 p-4 hover:bg-gray-50 transition-colors text-left',
                selectedUser?.id === conv.other_user?.id ? 'bg-emerald-50 border-r-2 border-emerald-600' : '',
              ]"
            >
              <img
                :src="avatarUrl(conv.other_user?.name, conv.other_user?.profileImage)"
                class="h-12 w-12 rounded-2xl object-cover shrink-0"
                :alt="conv.other_user?.name"
              />
              <div class="flex-1 min-w-0">
                <p class="font-bold text-gray-900 text-sm truncate">{{ conv.other_user?.name }}</p>
                <p class="text-xs text-gray-400 truncate">{{ conv.last_message?.content || 'Nouvelle conversation' }}</p>
              </div>
              <div class="text-[10px] text-gray-400 shrink-0">
                {{ formatDate(conv.last_message?.created_at) }}
              </div>
            </button>
          </div>
        </div>
      </div>

      <!-- Chat area -->
      <div class="flex-1 bg-white rounded-3xl border border-gray-100 shadow-sm flex flex-col overflow-hidden">
        <!-- No conversation selected -->
        <div v-if="!selectedUser" class="flex-1 flex flex-col items-center justify-center text-center p-8">
          <div class="w-20 h-20 bg-emerald-50 rounded-3xl flex items-center justify-center mb-4">
            <i class="fa-regular fa-comments text-emerald-600 text-3xl"></i>
          </div>
          <h3 class="font-bold text-gray-800 text-lg mb-2">Sélectionnez une conversation</h3>
          <p class="text-sm text-gray-500 max-w-xs">Choisissez une conversation dans la liste ou visitez un profil pour envoyer un message.</p>
        </div>

        <template v-else>
          <!-- Chat header -->
          <div class="p-5 border-b border-gray-50 flex items-center gap-4">
            <img
              :src="avatarUrl(selectedUser.name, selectedUser.profileImage)"
              class="h-12 w-12 rounded-2xl object-cover"
              :alt="selectedUser.name"
            />
            <div>
              <p class="font-bold text-gray-900">{{ selectedUser.name }}</p>
              <p class="text-xs text-emerald-600 font-medium uppercase">{{ selectedUser.role }}</p>
            </div>
            <RouterLink
              :to="`/users/${selectedUser.id}`"
              class="ml-auto text-sm font-semibold text-emerald-700 hover:text-emerald-800 no-underline"
            >
              Voir profil
            </RouterLink>
          </div>

          <!-- Messages -->
          <div class="flex-1 overflow-y-auto p-5 space-y-4">
            <div v-if="loadingMessages" class="space-y-3">
              <div v-for="n in 5" :key="n" class="flex gap-2 animate-pulse" :class="n % 2 === 0 ? 'flex-row-reverse' : ''">
                <div class="h-8 w-8 bg-gray-200 rounded-xl shrink-0"></div>
                <div class="max-w-xs">
                  <div class="h-10 bg-gray-200 rounded-2xl w-48"></div>
                </div>
              </div>
            </div>

            <div v-else-if="messages.length === 0" class="flex flex-col items-center justify-center h-40 text-center">
              <i class="fa-regular fa-comment text-gray-300 text-3xl mb-2"></i>
              <p class="text-sm text-gray-400">Aucun message. Démarrez la conversation !</p>
            </div>

            <div
              v-for="msg in messages"
              :key="msg.id"
              class="flex gap-3 group"
              :class="msg.sender_id === currentUser?.id ? 'flex-row-reverse' : ''"
            >
              <img
                :src="avatarUrl(
                  msg.sender_id === currentUser?.id ? currentUser?.name : selectedUser?.name,
                  msg.sender_id === currentUser?.id ? currentUser?.profileImage : selectedUser?.profileImage
                )"
                class="h-8 w-8 rounded-xl object-cover shrink-0 mt-1"
                alt="Avatar"
              />
              <div class="max-w-sm">
                <div
                  :class="[
                    'px-4 py-2.5 rounded-2xl text-sm leading-relaxed',
                    msg.sender_id === currentUser?.id
                      ? 'bg-emerald-700 text-white rounded-tr-sm'
                      : 'bg-gray-100 text-gray-800 rounded-tl-sm',
                  ]"
                >
                  {{ msg.content }}
                </div>
                <div class="flex items-center gap-2 mt-1 opacity-0 group-hover:opacity-100 transition"
                  :class="msg.sender_id === currentUser?.id ? 'flex-row-reverse' : ''"
                >
                  <span class="text-[10px] text-gray-400">{{ formatTime(msg.created_at) }}</span>
                  <button
                    v-if="msg.sender_id === currentUser?.id"
                    @click="deleteMessage(msg.id)"
                    class="text-[10px] text-red-400 hover:text-red-600"
                  >
                    <i class="fa-solid fa-trash-can"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Error -->
          <div v-if="error" class="px-5 py-2 bg-red-50 text-red-600 text-xs">{{ error }}</div>

          <!-- Input -->
          <div class="p-4 border-t border-gray-50">
            <div class="flex gap-3">
              <input
                v-model="newMessage"
                type="text"
                placeholder="Écrire un message..."
                class="flex-1 bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                @keyup.enter="sendMessage"
              />
              <button
                @click="sendMessage"
                :disabled="sendingMessage || !newMessage.trim()"
                class="bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-3 rounded-2xl font-bold transition disabled:opacity-40"
              >
                <i v-if="sendingMessage" class="fa-solid fa-spinner animate-spin"></i>
                <i v-else class="fa-solid fa-paper-plane"></i>
              </button>
            </div>
          </div>
        </template>
      </div>
    </div>
  </MainLayout>
</template>
