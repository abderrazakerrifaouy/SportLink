<script setup>
import { ref, onMounted } from 'vue'
import { useCommentStore } from '@/stores/commentStore'
import { useAuthStore } from '@/stores/AuthStore'
import { storeToRefs } from 'pinia'

const props = defineProps({
  postId: { type: Number, required: true },
})

const commentStore = useCommentStore()
const authStore = useAuthStore()
const { user } = storeToRefs(authStore)

const { comments, replies } = storeToRefs(commentStore)

const newComment = ref('')
const newReplies = ref({})
const showReplyInput = ref({})
const expandedReplies = ref({})
const loading = ref(false)
const submitting = ref(false)

onMounted(async () => {
  loading.value = true
  commentStore.reset()
  await commentStore.fetchComments(props.postId)
  loading.value = false
})

const submitComment = async () => {
  if (!newComment.value.trim()) return
  submitting.value = true
  try {
    await commentStore.addComment(props.postId, newComment.value.trim())
    newComment.value = ''
  } finally {
    submitting.value = false
  }
}

const toggleReplyInput = (commentId) => {
  showReplyInput.value[commentId] = !showReplyInput.value[commentId]
  if (!newReplies.value[commentId]) newReplies.value[commentId] = ''
}

const submitReply = async (commentId) => {
  const text = newReplies.value[commentId]
  if (!text?.trim()) return
  await commentStore.addReply(commentId, text.trim())
  newReplies.value[commentId] = ''
  showReplyInput.value[commentId] = false
  expandedReplies.value[commentId] = true
}

const toggleReplies = async (commentId) => {
  if (!replies.value[commentId]) {
    await commentStore.fetchReplies(commentId)
  }
  expandedReplies.value[commentId] = !expandedReplies.value[commentId]
}

const handleDeleteComment = async (commentId) => {
  if (confirm('Supprimer ce commentaire ?')) {
    await commentStore.deleteComment(commentId)
  }
}

const handleDeleteReply = async (commentId, replyId) => {
  if (confirm('Supprimer cette réponse ?')) {
    await commentStore.deleteReply(commentId, replyId)
  }
}

const avatarUrl = (name) =>
  `https://ui-avatars.com/api/?name=${encodeURIComponent(name || 'U')}&bg=064E3B&color=fff`

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script>

<template>
  <div class="border-t border-gray-100 pt-4 mt-2">
    <!-- Add comment -->
    <div class="flex gap-3 mb-4">
      <img
        :src="avatarUrl(user?.name)"
        class="h-9 w-9 rounded-xl object-cover shrink-0"
        alt="Me"
      />
      <div class="flex-1 flex gap-2">
        <input
          v-model="newComment"
          type="text"
          placeholder="Ajouter un commentaire..."
          class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
          @keyup.enter="submitComment"
        />
        <button
          @click="submitComment"
          :disabled="submitting || !newComment.trim()"
          class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 rounded-xl text-sm font-semibold disabled:opacity-40 transition"
        >
          <i v-if="submitting" class="fa-solid fa-spinner animate-spin"></i>
          <i v-else class="fa-solid fa-paper-plane"></i>
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="space-y-3">
      <div v-for="n in 3" :key="n" class="flex gap-3 animate-pulse">
        <div class="h-9 w-9 bg-gray-200 rounded-xl shrink-0"></div>
        <div class="flex-1 space-y-2 pt-1">
          <div class="h-3 bg-gray-200 rounded w-1/4"></div>
          <div class="h-3 bg-gray-100 rounded w-3/4"></div>
        </div>
      </div>
    </div>

    <!-- Comments list -->
    <div v-else class="space-y-4">
      <div
        v-for="comment in comments"
        :key="comment.id"
        class="group"
      >
        <div class="flex gap-3">
          <img
            :src="comment.user?.profileImage || avatarUrl(comment.user?.name)"
            class="h-9 w-9 rounded-xl object-cover shrink-0"
            :alt="comment.user?.name"
          />
          <div class="flex-1">
            <div class="bg-gray-50 rounded-2xl px-4 py-2.5">
              <div class="flex items-center justify-between mb-1">
                <span class="font-bold text-sm text-gray-800">{{ comment.user?.name }}</span>
                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition">
                  <span class="text-[10px] text-gray-400">{{ formatDate(comment.created_at) }}</span>
                  <button
                    v-if="user?.id === comment.user?.id"
                    @click="handleDeleteComment(comment.id)"
                    class="text-red-400 hover:text-red-600 text-xs"
                  >
                    <i class="fa-solid fa-trash-can"></i>
                  </button>
                </div>
              </div>
              <p class="text-sm text-gray-700 leading-relaxed">{{ comment.content }}</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4 mt-1 px-2">
              <button
                @click="toggleReplyInput(comment.id)"
                class="text-[11px] font-bold text-gray-400 hover:text-emerald-600 transition"
              >
                Répondre
              </button>
              <button
                v-if="comment.replies_count > 0 || replies[comment.id]?.length"
                @click="toggleReplies(comment.id)"
                class="text-[11px] font-bold text-emerald-600 hover:text-emerald-800 transition"
              >
                <i class="fa-solid fa-chevron-down text-[9px] mr-1" :class="{ 'rotate-180': expandedReplies[comment.id] }"></i>
                {{ replies[comment.id]?.length || comment.replies_count }}
                réponse{{ (replies[comment.id]?.length || comment.replies_count) !== 1 ? 's' : '' }}
              </button>
            </div>

            <!-- Reply input -->
            <div v-if="showReplyInput[comment.id]" class="flex gap-2 mt-2 ml-2">
              <input
                v-model="newReplies[comment.id]"
                type="text"
                :placeholder="`Répondre à ${comment.user?.name}...`"
                class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                @keyup.enter="submitReply(comment.id)"
              />
              <button
                @click="submitReply(comment.id)"
                class="bg-emerald-700 hover:bg-emerald-800 text-white px-3 py-2 rounded-xl text-sm transition"
              >
                <i class="fa-solid fa-paper-plane"></i>
              </button>
            </div>

            <!-- Replies -->
            <div v-if="expandedReplies[comment.id] && replies[comment.id]" class="mt-3 ml-4 space-y-3">
              <div
                v-for="reply in replies[comment.id]"
                :key="reply.id"
                class="flex gap-2 group/reply"
              >
                <img
                  :src="reply.user?.profileImage || avatarUrl(reply.user?.name)"
                  class="h-7 w-7 rounded-lg object-cover shrink-0"
                  :alt="reply.user?.name"
                />
                <div class="flex-1">
                  <div class="bg-gray-50 rounded-xl px-3 py-2">
                    <div class="flex items-center justify-between mb-0.5">
                      <span class="font-bold text-xs text-gray-800">{{ reply.user?.name }}</span>
                      <div class="flex items-center gap-2 opacity-0 group-hover/reply:opacity-100 transition">
                        <span class="text-[9px] text-gray-400">{{ formatDate(reply.created_at) }}</span>
                        <button
                          v-if="user?.id === reply.user?.id"
                          @click="handleDeleteReply(comment.id, reply.id)"
                          class="text-red-400 hover:text-red-600 text-[10px]"
                        >
                          <i class="fa-solid fa-trash-can"></i>
                        </button>
                      </div>
                    </div>
                    <p class="text-xs text-gray-700">{{ reply.content }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="!loading && comments.length === 0" class="text-center py-6">
        <i class="fa-regular fa-comment text-gray-300 text-3xl mb-2"></i>
        <p class="text-sm text-gray-400">Aucun commentaire. Soyez le premier !</p>
      </div>
    </div>
  </div>
</template>
