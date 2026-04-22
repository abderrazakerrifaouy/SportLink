<template>
  <article class="bg-white rounded-lg shadow-sm border border-gray-300 mb-4 mx-auto font-sans text-[#1c1e21] max-w-[600px]">
    <div class="p-3 flex items-center justify-between">
      <div class="flex gap-2">
        <div class="relative cursor-pointer">
          <img :src="authorAvatar" class="w-10 h-10 rounded-full object-cover border border-gray-100">
          <div class="absolute bottom-0.5 right-0.5 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></div>
        </div>
        <div class="flex flex-col justify-center">
          <p class="font-bold text-[15px] hover:underline cursor-pointer leading-tight">{{ authorName }}</p>
          <span class="text-[12px] text-gray-500">{{ post.created_at }}</span>
        </div>
      </div>
      <button v-if="isAuthor" @click="handleDelete" class="p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-colors">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" /></svg>
      </button>
    </div>

    <div class="px-3 pb-3 text-[15px] leading-normal whitespace-pre-wrap">
      {{ post.content }}
    </div>

    <div v-if="post.media?.length" class="border-y border-gray-100 bg-[#f0f2f5]">
      <div :class="['grid gap-[2px]', post.media.length === 1 ? 'grid-cols-1' : 'grid-cols-2']">
        <div v-for="(media, index) in post.media.slice(0, 4)" :key="media.id" class="relative aspect-square overflow-hidden bg-black flex items-center">
          <video v-if="media.type === 'VIDEO'" controls class="w-full h-full object-contain"><source :src="media.url"></video>
          <img v-else :src="media.url" class="w-full h-full object-cover cursor-pointer hover:opacity-95 transition">
          <div v-if="index === 3 && post.media.length > 4" class="absolute inset-0 bg-black/60 flex items-center justify-center text-white text-2xl font-bold">+{{ post.media.length - 4 }}</div>
        </div>
      </div>
    </div>

    <div class="px-3 py-2.5 flex items-center justify-between text-[14px] text-gray-500">
      <div class="flex items-center gap-1.5 cursor-pointer hover:underline">
        <div class="flex -space-x-1">
          <span v-for="(emoji, type) in topReactions" :key="type"
                :class="['w-4 h-4 rounded-full flex items-center justify-center text-[9px] text-white border border-white', getReactionBgClass(type)]">
            {{ emoji }}
          </span>
        </div>
        <span class="font-medium">{{ totalReactions }}</span>
      </div>
      <span class="hover:underline cursor-pointer">{{ post.comments_count || 0 }} comments</span>
    </div>

    <div class="px-1 py-1 border-t border-gray-200 mx-3 flex items-center justify-between relative">
      <div class="relative flex-1 group" @mouseenter="showReactionPicker = true" @mouseleave="showReactionPicker = false">
        <button
          @click="toggleReaction"
          class="w-full flex items-center justify-center gap-2 py-2 hover:bg-gray-100 rounded-md transition font-semibold text-[14px]"
          :style="{ color: userReactionColor }"
        >
          <template v-if="userReaction">
            <span class="text-xl scale-110">{{ getReactionEmoji(userReaction) }}</span>
            <span>{{ userReactionLabel }}</span>
          </template>
          <template v-else>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M14 10h4.708c.94 0 1.708.768 1.708 1.708 0 .19-.032.379-.094.56l-2.053 6.16c-.347 1.04-1.32 1.74-2.417 1.74H7V10l4.272-4.272a2 2 0 012.828 0l1.172 1.172A2 2 0 0114 10zM7 10H4a1 1 0 00-1 1v9a1 1 0 001 1h3V10z" />
            </svg>
            <span>Like</span>
          </template>
        </button>

        <Transition name="pop">
          <div v-if="showReactionPicker" class="absolute bottom-full mb-2 left-0 p-1.5 bg-white rounded-full shadow-xl border border-gray-200 flex gap-1 z-50">
            <button
              v-for="type in ['LIKE', 'LOVE', 'HAHA', 'WOW', 'SAD', 'GRR']"
              :key="type"
              @click="selectReaction(type)"
              class="text-3xl hover:scale-150 transition-transform duration-200 origin-bottom px-1"
            >
              {{ getReactionEmoji(type) }}
            </button>
          </div>
        </Transition>
      </div>

      <button class="flex-1 flex items-center justify-center gap-2 py-2 hover:bg-gray-100 rounded-md transition text-gray-600 font-semibold text-[14px]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
        Comment
      </button>
    </div>

    <div class="px-4 py-3 border-t border-gray-100 bg-[#f9fafb]">
       <CommentSection :post-id="post.id" :initial-comments="[]" />
    </div>
  </article>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePostStore } from '@/stores/PostStore'
import { useAuthStore } from '@/stores/AuthStore'
import CommentSection from './CommentSection.vue'

const props = defineProps({
  post: { type: Object, required: true }
})

const emit = defineEmits(['deleted'])
const postStore = usePostStore()
const authStore = useAuthStore()

const showReactionPicker = ref(false)

// Logic
const isAuthor = computed(() => props.post.author?.id === authStore.user?.id)
const authorAvatar = computed(() => props.post.author?.profile_image || '/default-avatar.png')
const authorName = computed(() => props.post.author?.name || 'User')

// User Reaction State
const userReaction = computed(() => {
  const reaction = postStore.getUserReactions(props.post.id)
  return reaction.length > 0 ? reaction[0].type : null
})

const userReactionLabel = computed(() => {
  if (!userReaction.value) return 'Like'
  const labels = { LIKE: 'Like', LOVE: 'Love', HAHA: 'Haha', WOW: 'Wow', SAD: 'Sad', GRR: 'Angry' }
  return labels[userReaction.value]
})

const userReactionColor = computed(() => {
  const colors = { LIKE: '#1877f2', LOVE: '#f02849', HAHA: '#f7b928', WOW: '#f7b928', SAD: '#f7b928', GRR: '#e07a5f' }
  return userReaction.value ? colors[userReaction.value] : 'inherit'
})

// Stats Logic
const totalReactions = computed(() => props.post.reactions_summary?.total || 0)

const topReactions = computed(() => {
  const summary = props.post.reactions_summary || {}
  return Object.entries(summary)
    .filter(([key, val]) => val > 0 && key !== 'total')
    .sort((a, b) => b[1] - a[1])
    .slice(0, 3)
    .reduce((obj, [key]) => {
      const type = key.replace('s', '').toUpperCase()
      obj[type] = getReactionEmoji(type)
      return obj
    }, {})
})

const getReactionEmoji = (type) => {
  return { LIKE: '👍', LOVE: '❤️', HAHA: '😂', WOW: '😮', SAD: '😢', GRR: '😠' }[type] || '👍'
}

const getReactionBgClass = (type) => {
  return { LIKE: 'bg-[#1877f2]', LOVE: 'bg-[#f02849]', HAHA: 'bg-[#f7b928]', WOW: 'bg-[#f7b928]', SAD: 'bg-[#f7b928]' }[type] || 'bg-gray-400'
}

// Handlers
const selectReaction = async (type) => {
  showReactionPicker.value = false
  // Ila dghat 3la nefs l-emoji li deja khtar, ghadi t-hayed (Toggle off)
  const newType = (userReaction.value === type) ? null : type
  await postStore.toggleReaction(props.post.id, newType)
}

const toggleReaction = async () => {
  // Logic: Click simple -> Ila kyna reaction ghadi t-hayed (null), ila makantch ghadi t-zad 'LIKE'
  const newType = userReaction.value ? null : 'LIKE'
  await postStore.toggleReaction(props.post.id, newType)
}

const handleDelete = async () => {
  if (confirm('Delete this post?')) {
    await postStore.deletePost(props.post.id)
    emit('deleted')
  }
}
</script>

<style scoped>
.pop-enter-active, .pop-leave-active {
  transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.pop-enter-from, .pop-leave-to {
  opacity: 0;
  transform: translateY(12px) scale(0.85);
}
</style>
