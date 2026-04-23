<template>
  <div class="w-full" :class="depth > 0 ? 'mt-2' : ''">
    <div class="flex gap-2 items-start">
      <img
        :src="author.profile_image || '/default-avatar.png'"
        class="w-8 h-8 rounded-full border border-gray-200 object-cover"
        alt="Comment author avatar"
      >

      <div class="flex-1 min-w-0">
        <div class="inline-block max-w-full bg-gray-100 rounded-2xl px-3 py-2">
          <p class="text-xs font-semibold text-gray-900 leading-tight">{{ author.name || 'Unknown' }}</p>
          <p class="text-sm text-gray-800 wrap-break-word mt-0.5">{{ node.content }}</p>
        </div>

        <div class="mt-1 flex items-center gap-3 pl-1 text-xs text-gray-500">
          <ReactionPicker
            :user-reaction="node.user_reaction || null"
            @select="(type) => onReact(type)"
            @toggle="onToggleLike"
          />

          <button
            v-if="canReply"
            @click="toggleReply"
            class="font-semibold hover:underline"
            type="button"
          >
            Reply
          </button>

          <span>{{ node.created_at || '' }}</span>
        </div>

        <Transition name="fade-slide">
          <div v-if="showReplyInput" class="mt-2 flex items-center gap-2">
            <input
              v-model="replyContent"
              @keyup.enter="submitReply"
              placeholder="Write a reply..."
              class="flex-1 bg-white border border-gray-200 rounded-full px-3 py-1.5 text-sm outline-none focus:border-blue-400"
            >
            <button
              @click="submitReply"
              type="button"
              class="w-8 h-8 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition flex items-center justify-center"
              aria-label="Send reply"
            >
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M3.4 20.4l17.45-7.48c.6-.25.6-1.09 0-1.34L3.4 4.1c-.55-.23-1.13.29-.96.86l1.72 5.78a.75.75 0 00.72.54h8.07a.75.75 0 010 1.5H4.88a.75.75 0 00-.72.54l-1.72 5.78c-.17.57.41 1.09.96.86z" />
              </svg>
            </button>
          </div>
        </Transition>

        <div v-if="children.length" class="mt-2 pl-4 border-l border-gray-200 space-y-1">
          <CommentThreadItem
            v-for="child in children"
            :key="child.id"
            :node="child"
            :post-id="postId"
            :depth="depth + 1"
            @reply-added="$emit('reply-added')"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useCommentStore } from '@/stores/commentStore'
import ReactionPicker from './ReactionPicker.vue'

defineOptions({
  name: 'CommentThreadItem'
})

const props = defineProps({
  node: {
    type: Object,
    required: true
  },
  postId: {
    type: Number,
    required: true
  },
  depth: {
    type: Number,
    default: 0
  }
})

const emit = defineEmits(['reply-added'])

const commentStore = useCommentStore()
const showReplyInput = ref(false)
const replyContent = ref('')

const author = computed(() => props.node.user || props.node.author || {})
const children = computed(() => props.node.replies || props.node.children || [])
const canReply = computed(() => props.node.node_type !== 'reply')

const toggleReply = () => {
  showReplyInput.value = !showReplyInput.value
}

const submitReply = async () => {
  if (!replyContent.value.trim()) return

  await commentStore.addReply(props.postId, props.node.id, replyContent.value)
  replyContent.value = ''
  showReplyInput.value = false
  emit('reply-added')
}

const onReact = async (type) => {
  await commentStore.toggleReaction(props.postId, props.node.id, type, props.node.node_type)
}

const onToggleLike = async () => {
  const selectedType = props.node.user_reaction || 'LIKE'
  await commentStore.toggleReaction(props.postId, props.node.id, selectedType, props.node.node_type)
}
</script>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.2s ease;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
