<template>
  <div class="mt-4">
    <div class="flex gap-2 mb-3">
      <input
        v-model="commentText"
        type="text"
        placeholder="Write a comment..."
        class="flex-1 px-4 py-2 bg-gray-50 border border-transparent rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none"
        @keyup.enter="submitComment"
      >
      <button
        v-if="commentText?.trim()"
        @click="submitComment"
        class="px-3 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
        </svg>
      </button>
    </div>

    <div v-if="comments.length > 0" class="space-y-3 pl-4 border-l-2 border-gray-100">
      <div
        v-for="comment in displayedComments"
        :key="comment.id"
        class="flex gap-2"
      >
        <img
          :src="getUserAvatar(comment.user)"
          class="w-8 h-8 rounded-full object-cover"
        >
        <div class="flex-1 bg-gray-50 rounded-xl px-3 py-2">
          <p class="text-sm font-bold text-gray-900">{{ getUserName(comment.user) }}</p>
          <p class="text-sm text-gray-700">{{ comment.content }}</p>
        </div>
      </div>

      <button
        v-if="comments.length > 2"
        @click="toggleExpanded"
        class="text-sm font-medium text-blue-600 hover:underline"
      >
        {{ expanded ? 'Show less' : `View all ${comments.length} comments` }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useCommentStore } from '@/stores/commentStore'

const props = defineProps({
  postId: {
    type: Number,
    required: true
  },
  initialComments: {
    type: Array,
    default: () => []
  }
})

const commentStore = useCommentStore()

const commentText = ref('')
const expanded = ref(false)

const comments = computed(() => commentStore.getComments(props.postId))

const displayedComments = computed(() => {
  return expanded.value ? comments.value : comments.value.slice(0, 2)
})

watch(() => props.postId, async () => {
  if (!commentStore.comments[props.postId]) {
    await commentStore.fetchComments(props.postId)
  }
}, { immediate: true })

const toggleExpanded = () => {
  expanded.value = !expanded.value
}

const getUserAvatar = (user) => {
  if (!user) return '/default-avatar.png'
  return user.profileImage || user.profile_image || '/default-avatar.png'
}

const getUserName = (user) => {
  if (!user) return 'Unknown'
  return user.name || 'Unknown'
}

const submitComment = async () => {
  const content = commentText.value?.trim()
  if (!content) return

  try {
    await commentStore.addComment(props.postId, content)
    commentText.value = ''
  } catch (error) {
    console.error('Failed to add comment:', error)
  }
}
</script>