<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePostStore } from '@/stores/PostStore'
import { useAuthStore } from '@/stores/AuthStore'
import { storeToRefs } from 'pinia'
import MainLayout from '@/layouts/MainLayout.vue'
import CommentSection from '@/components/CommentSection.vue'
import ImagePlayer from '@/components/ImagePlayer.vue'
import VideoPlayer from '@/components/VideoPlayer.vue'
import reactionService from '@/services/reactionService'

const route = useRoute()
const router = useRouter()
const postStore = usePostStore()
const authStore = useAuthStore()
const { user } = storeToRefs(authStore)

const post = ref(null)
const loading = ref(true)
const liked = ref(false)
const likeCount = ref(0)
const likeId = ref(null)
const isLiking = ref(false)
const isDeleting = ref(false)

const postId = computed(() => Number(route.params.id))
const isOwner = computed(() => user.value?.id === post.value?.author?.id)

const totalMedia = computed(() => post.value?.media?.length || 0)
const gridClass = computed(() => {
  const c = totalMedia.value
  if (c === 1) return 'grid-cols-1'
  if (c === 2) return 'grid-cols-2 h-72'
  return 'grid-cols-2 grid-rows-2 h-96'
})

const loadPost = async () => {
  loading.value = true
  try {
    post.value = await postStore.fetchPostById(postId.value)
    liked.value = post.value?.user_reaction?.id != null
    likeCount.value = post.value?.reactions_summary?.likes || 0
    likeId.value = post.value?.user_reaction?.id || null
  } catch (err) {
    console.error('Failed to load post:', err)
  } finally {
    loading.value = false
  }
}

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
        reactable_id: postId.value,
        type: 'LIKE',
      })
      liked.value = true
      likeCount.value++
      likeId.value = response.data?.id || null
    }
  } finally {
    isLiking.value = false
  }
}

const handleDelete = async () => {
  if (!confirm('Supprimer ce post ?')) return
  isDeleting.value = true
  try {
    await postStore.deletePost(postId.value)
    router.push('/')
  } finally {
    isDeleting.value = false
  }
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const avatarUrl = (name, img) =>
  img || `https://ui-avatars.com/api/?name=${encodeURIComponent(name || 'U')}&bg=064E3B&color=fff`

onMounted(loadPost)
</script>

<template>
  <MainLayout>
    <!-- Back button -->
    <div class="mb-5">
      <button
        @click="router.back()"
        class="flex items-center gap-2 text-sm text-gray-500 hover:text-emerald-700 font-semibold transition"
      >
        <i class="fa-solid fa-arrow-left"></i> Retour
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="bg-white rounded-3xl border border-gray-100 p-8 animate-pulse space-y-4">
      <div class="flex items-center gap-3">
        <div class="h-14 w-14 bg-gray-200 rounded-2xl"></div>
        <div class="space-y-2 flex-1">
          <div class="h-4 bg-gray-200 rounded w-1/4"></div>
          <div class="h-3 bg-gray-100 rounded w-1/5"></div>
        </div>
      </div>
      <div class="h-4 bg-gray-100 rounded w-full"></div>
      <div class="h-4 bg-gray-100 rounded w-3/4"></div>
      <div class="aspect-video bg-gray-100 rounded-2xl"></div>
    </div>

    <div v-else-if="post" class="space-y-5">
      <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="p-6 flex items-start justify-between">
          <div
            class="flex items-center gap-4 cursor-pointer"
            @click="router.push(`/users/${post.author?.id}`)"
          >
            <img
              :src="avatarUrl(post.author?.name, post.author?.profileImage)"
              class="h-14 w-14 rounded-2xl object-cover border border-gray-100 shadow-sm"
              :alt="post.author?.name"
            />
            <div>
              <h2 class="font-black text-gray-900 text-lg hover:text-emerald-700 transition">{{ post.author?.name }}</h2>
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                <span class="text-emerald-600">{{ post.author?.role }}</span>
                <span class="mx-2 text-gray-300">•</span>
                {{ formatDate(post.created_at) }}
              </p>
            </div>
          </div>

          <div class="flex gap-2" v-if="isOwner">
            <RouterLink
              :to="`/posts/${post.id}/edit`"
              class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50 transition no-underline"
            >
              <i class="fa-regular fa-pen-to-square text-emerald-500"></i> Modifier
            </RouterLink>
            <button
              @click="handleDelete"
              :disabled="isDeleting"
              class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold border border-red-200 text-red-600 hover:bg-red-50 transition disabled:opacity-50"
            >
              <i class="fa-regular fa-trash-can"></i>
              <span v-if="isDeleting">Suppression...</span>
              <span v-else>Supprimer</span>
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="px-6 pb-6">
          <p class="text-gray-800 text-base leading-relaxed">{{ post.content }}</p>
        </div>

        <!-- Media -->
        <div v-if="totalMedia > 0" class="px-6 pb-4">
          <div :class="['grid gap-1.5 rounded-2xl overflow-hidden bg-gray-50', gridClass]">
            <div
              v-for="(item, index) in post.media"
              :key="index"
              :class="['relative group', totalMedia === 3 && index === 0 ? 'row-span-2' : '']"
            >
              <ImagePlayer v-if="item.type === 'IMAGE'" :src="item.url" class="h-full w-full" />
              <VideoPlayer v-else-if="item.type === 'VIDEO'" :src="item.url" class="h-full w-full" />
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="px-6 py-4 border-t border-gray-50 flex items-center gap-4">
          <button
            @click="toggleLike"
            :disabled="isLiking"
            :class="[
              'flex items-center gap-2 px-5 py-2.5 rounded-2xl font-bold text-sm transition-all',
              liked ? 'bg-red-50 text-red-500' : 'text-gray-500 hover:bg-red-50 hover:text-red-500',
            ]"
          >
            <i :class="liked ? 'fa-solid fa-heart' : 'fa-regular fa-heart'"></i>
            {{ likeCount }} J'aime{{ likeCount > 1 ? 's' : '' }}
          </button>
          <div class="flex items-center gap-2 px-5 py-2.5 rounded-2xl text-gray-500 text-sm font-bold bg-gray-50">
            <i class="fa-regular fa-comment"></i>
            {{ post.comments_count || 0 }} commentaire{{ (post.comments_count || 0) > 1 ? 's' : '' }}
          </div>
        </div>

        <!-- Comments -->
        <div class="px-6 pb-6">
          <CommentSection :postId="post.id" />
        </div>
      </div>
    </div>

    <!-- Not found -->
    <div v-else class="bg-white rounded-3xl border border-gray-100 p-10 text-center shadow-sm">
      <i class="fa-solid fa-triangle-exclamation text-gray-300 text-5xl mb-4"></i>
      <h3 class="font-bold text-gray-800 mb-2">Post introuvable</h3>
      <RouterLink to="/" class="text-emerald-700 font-semibold text-sm hover:underline">Retour à l'accueil</RouterLink>
    </div>
  </MainLayout>
</template>
