<template>
  <div class="space-y-3 p-3">
    <div class="flex items-center gap-2">
      <input
        v-model="newComment"
        @keyup.enter="postComment"
        placeholder="Write a comment..."
        class="flex-1 bg-white border border-gray-200 rounded-full px-4 py-2 text-sm outline-none focus:border-blue-400"
        :disabled="submitting"
      >
      <button
        @click="postComment"
        type="button"
        :disabled="submitting"
        class="w-9 h-9 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition flex items-center justify-center disabled:cursor-not-allowed disabled:opacity-60"
        aria-label="Send comment"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M3.4 20.4l17.45-7.48c.6-.25.6-1.09 0-1.34L3.4 4.1c-.55-.23-1.13.29-.96.86l1.72 5.78a.75.75 0 00.72.54h8.07a.75.75 0 010 1.5H4.88a.75.75 0 00-.72.54l-1.72 5.78c-.17.57.41 1.09.96.86z" />
        </svg>
      </button>
    </div>

    <p v-if="actionError" class="px-1 text-sm font-medium text-rose-600">
      {{ actionError }}
    </p>

    <div v-if="comments.length" class="space-y-3">
      <CommentThreadItem
        v-for="comment in comments"
        :key="comment.id"
        :node="comment"
        :post-id="postId"
        :depth="0"
        @changed="$emit('changed')"
      />
    </div>

    <p v-else class="text-sm text-gray-500 px-1">No comments yet. Start the conversation.</p>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useCommentStore } from '@/stores/commentStore'
import CommentThreadItem from './CommentThreadItem.vue'

const emit = defineEmits(['changed'])

const props = defineProps({
  postId: {
    type: Number,
    required: true
  }
})

const commentStore = useCommentStore()
const newComment = ref('')
const submitting = ref(false)
const actionError = ref('')

const comments = computed(() => commentStore.getComments(props.postId))

onMounted(() => commentStore.fetchComments(props.postId))

const postComment = async () => {
  if (!newComment.value.trim() || submitting.value) return

  submitting.value = true
  actionError.value = ''

  try {
    await commentStore.addComment(props.postId, newComment.value)
    newComment.value = ''
    emit('changed')
  } catch (error) {
    actionError.value = error?.response?.data?.message || 'Failed to add comment.'
  } finally {
    submitting.value = false
  }
}
</script>
