<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import ImagePlayer from './ImagePlayer.vue'
import VideoPlayer from './VideoPlayer.vue'
import CommentSection from './CommentSection.vue'
import reactionService from '@/services/reactionService'
import { useAuthStore } from '@/stores/AuthStore'
import { usePostStore } from '@/stores/PostStore'
import { storeToRefs } from 'pinia'

const props = defineProps({
  post: { type: Object, required: true },
  loading: { type: Boolean, default: false },
  showFullActions: { type: Boolean, default: false },
})

const router = useRouter()
const authStore = useAuthStore()
const postStore = usePostStore()
const { user } = storeToRefs(authStore)

const showComments = ref(props.showFullActions)
const liked = ref(props.post.user_reaction?.id != null)
const likeCount = ref(props.post.reactions_summary?.likes || 0)
const likeId = ref(props.post.user_reaction?.id || null)
const isLiking = ref(false)
const showMenu = ref(false)
const isDeleting = ref(false)

const totalMedia = computed(() => props.post.media?.length || 0)

const gridClass = computed(() => {
  const count = totalMedia.value
  if (count === 1) return 'grid-cols-1'
  if (count === 2) return 'grid-cols-2 h-72'
  return 'grid-cols-2 grid-rows-2 h-96'
})

const isOwner = computed(() => user.value?.id === props.post.author?.id)

const toggleLike = async () => {
  if (isLiking.value) return
  isLiking.value = true
  try {
    if (liked.value && likeId.value) {
      await reactionService.deleteReaction(likeId.value)
      liked.value = false
      likeCount.value = Math.max(0, likeCount.value - 1)
      likeId.value = null
    } else {
      const response = await reactionService.createReaction({
        reactable_type: 'post',
        reactable_id: props.post.id,
        type: 'LIKE',
      })
      liked.value = true
      likeCount.value++
      likeId.value = response.data?.id || null
    }
  } catch (err) {
    console.error('Reaction error:', err)
  } finally {
    isLiking.value = false
  }
}

const toggleComments = () => {
  showComments.value = !showComments.value
}

const goToDetail = () => {
  router.push(`/posts/${props.post.id}`)
}

const goToProfile = () => {
  router.push(`/users/${props.post.author?.id}`)
}

const handleEdit = () => {
  router.push(`/posts/${props.post.id}/edit`)
  showMenu.value = false
}

const handleDelete = async () => {
  if (!confirm('Supprimer ce post ?')) return
  isDeleting.value = true
  showMenu.value = false
  try {
    await postStore.deletePost(props.post.id)
  } catch (err) {
    console.error('Delete error:', err)
  } finally {
    isDeleting.value = false
  }
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}
</script>

<template>
  <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden mb-6 transition-all hover:shadow-md mx-auto relative">

    <!-- Skeleton -->
    <div v-if="loading" class="animate-pulse p-5">
      <div class="flex items-start gap-3 mb-4">
        <div class="h-12 w-12 bg-gray-200 rounded-2xl shrink-0"></div>
        <div class="flex-1 space-y-2 py-1">
          <div class="h-4 bg-gray-200 rounded w-1/3"></div>
          <div class="h-3 bg-gray-100 rounded w-1/4"></div>
        </div>
      </div>
      <div class="aspect-video bg-gray-100 rounded-2xl"></div>
    </div>

    <div v-else>
      <!-- Header -->
      <div class="p-5 flex items-start justify-between">
        <div class="flex items-center gap-3 cursor-pointer" @click="goToProfile">
          <img
            :src="post.author?.profileImage || `https://ui-avatars.com/api/?name=${encodeURIComponent(post.author?.name || 'U')}&bg=064E3B&color=fff`"
            class="h-12 w-12 rounded-2xl object-cover bg-gray-50 border border-gray-100 shadow-sm"
            alt="Profile"
          />
          <div>
            <h3 class="font-bold text-gray-900 leading-tight hover:text-emerald-700 transition">
              {{ post.author?.name }}
            </h3>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-1">
              <span class="text-emerald-600">{{ post.author?.role }}</span>
              <span class="text-gray-300">•</span>
              {{ formatDate(post.created_at) }}
            </p>
          </div>
        </div>

        <!-- Menu -->
        <div class="relative">
          <button
            @click="showMenu = !showMenu"
            class="text-gray-400 hover:bg-gray-50 p-2 rounded-xl transition"
          >
            <i class="fa-solid fa-ellipsis-vertical"></i>
          </button>
          <div
            v-if="showMenu"
            class="absolute right-0 top-10 bg-white rounded-2xl border border-gray-100 shadow-xl z-20 overflow-hidden min-w-[160px]"
          >
            <button
              @click="goToDetail"
              class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 font-medium"
            >
              <i class="fa-regular fa-eye text-gray-400"></i> Voir le post
            </button>
            <template v-if="isOwner">
              <button
                @click="handleEdit"
                class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 font-medium"
              >
                <i class="fa-regular fa-pen-to-square text-emerald-500"></i> Modifier
              </button>
              <button
                @click="handleDelete"
                :disabled="isDeleting"
                class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 font-medium disabled:opacity-50"
              >
                <i class="fa-regular fa-trash-can"></i>
                <span v-if="isDeleting">Suppression...</span>
                <span v-else>Supprimer</span>
              </button>
            </template>
          </div>
          <!-- Backdrop to close menu -->
          <div v-if="showMenu" class="fixed inset-0 z-10" @click="showMenu = false"></div>
        </div>
      </div>

      <!-- Content -->
      <div class="px-5 pb-4 cursor-pointer" @click="goToDetail">
        <p class="text-gray-800 text-sm leading-relaxed">{{ post.content }}</p>
      </div>

      <!-- Media -->
      <div v-if="totalMedia > 0" class="px-5">
        <div :class="['grid gap-1.5 rounded-2xl overflow-hidden bg-gray-50', gridClass]">
          <div
            v-for="(item, index) in post.media.slice(0, 3)"
            :key="index"
            :class="['relative group', totalMedia === 3 && index === 0 ? 'row-span-2 h-full' : 'h-full']"
          >
            <ImagePlayer
              v-if="item.type === 'IMAGE'"
              :src="item.url"
              class="h-full w-full"
            />
            <VideoPlayer
              v-else-if="item.type === 'VIDEO'"
              :src="item.url"
              class="h-full w-full"
            />
            <div
              v-if="index === 2 && totalMedia > 3"
              class="absolute inset-0 bg-black/60 backdrop-blur-[2px] flex items-center justify-center transition group-hover:bg-black/70"
            >
              <span class="text-white font-black text-2xl">+{{ totalMedia - 3 }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="px-5 py-4 mt-2 flex items-center justify-between border-t border-gray-50">
        <div class="flex items-center gap-1">
          <!-- Like -->
          <button
            @click="toggleLike"
            :disabled="isLiking"
            :class="[
              'flex items-center gap-2 px-4 py-2 rounded-2xl transition-all font-bold text-xs group',
              liked
                ? 'bg-red-50 text-red-500'
                : 'text-gray-500 hover:bg-red-50 hover:text-red-500',
            ]"
          >
            <i
              :class="[
                'text-lg group-hover:scale-110 transition',
                liked ? 'fa-solid fa-heart' : 'fa-regular fa-heart',
              ]"
            ></i>
            {{ likeCount }}
          </button>

          <!-- Comment toggle -->
          <button
            @click="toggleComments"
            :class="[
              'flex items-center gap-2 px-4 py-2 rounded-2xl transition-all font-bold text-xs group',
              showComments
                ? 'bg-emerald-50 text-emerald-700'
                : 'text-gray-500 hover:bg-emerald-50 hover:text-emerald-700',
            ]"
          >
            <i class="fa-regular fa-comment text-lg group-hover:scale-110 transition"></i>
            {{ post.comments_count || 0 }}
          </button>
        </div>

        <button
          @click="goToDetail"
          class="flex items-center gap-2 text-emerald-700 bg-emerald-50/50 hover:bg-emerald-100 px-4 py-2 rounded-2xl transition-all font-black text-[10px] uppercase tracking-tighter"
        >
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
          Voir
        </button>
      </div>

      <!-- Comments Section -->
      <div v-if="showComments" class="px-5 pb-4">
        <CommentSection :postId="post.id" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: .5; }
}
</style>
