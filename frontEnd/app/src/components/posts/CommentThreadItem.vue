<template>
  <div class="w-full" :class="depth > 0 ? 'mt-2' : ''">
    <div class="flex gap-2 items-start">
      <img
        :src="author.profile_image || '/default-avatar.png'"
        class="w-8 h-8 rounded-full border border-gray-200 object-cover"
        alt="Comment author avatar"
      >

      <div class="flex-1 min-w-0">
        <div v-if="isEditing" class="inline-block max-w-full rounded-2xl border border-indigo-100 bg-white px-3 py-2 shadow-sm">
          <textarea
            v-model="editContent"
            rows="3"
            class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-800 outline-none focus:border-indigo-400"
          ></textarea>
          <div class="mt-2 flex flex-wrap gap-2">
            <button
              type="button"
              class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
              :disabled="savingNode"
              @click="saveNode"
            >
              {{ savingNode ? 'Saving...' : 'Save' }}
            </button>
            <button
              type="button"
              class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-slate-300"
              :disabled="savingNode"
              @click="cancelEdit"
            >
              Cancel
            </button>
          </div>
        </div>
        <div v-else class="inline-block max-w-full bg-gray-100 rounded-2xl px-3 py-2">
          <p class="text-xs font-semibold text-gray-900 leading-tight">{{ author.name || 'Unknown' }}</p>
          <p class="text-sm text-gray-800 wrap-break-word mt-0.5">{{ node.content }}</p>
        </div>

        <div class="mt-1 flex items-center gap-3 pl-1 text-xs text-gray-500 relative">
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

          <div v-if="canManageNode" class="relative">
            <button type="button" class="font-semibold hover:underline" @click.stop="showActionsMenu = !showActionsMenu">⋯</button>
            <div v-if="showActionsMenu" class="absolute left-0 top-full z-10 mt-2 w-32 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg">
              <button
                type="button"
                class="block w-full px-3 py-2 text-left text-xs font-semibold text-gray-700 hover:bg-gray-50"
                @click="startEdit"
              >
                Edit
              </button>
              <button
                type="button"
                class="block w-full px-3 py-2 text-left text-xs font-semibold text-rose-600 hover:bg-rose-50"
                @click="deleteNode"
              >
                Delete
              </button>
            </div>
          </div>

          <span>{{ node.created_at || '' }}</span>
        </div>

        <p v-if="actionError" class="mt-1 pl-1 text-xs font-medium text-rose-600">
          {{ actionError }}
        </p>

        <div v-if="reactionBreakdown.length" class="mt-1 pl-1 flex items-center gap-2 text-xs text-gray-500">
          <div class="flex items-center gap-1.5">
            <span
              v-for="item in reactionBreakdown"
              :key="item.type"
              class="w-4 h-4 flex items-center justify-center rounded-full bg-white text-[11px] shadow-sm"
            >
              {{ item.emoji }}
            </span>
          </div>
          <span class="font-medium">{{ totalReactions }}</span>
        </div>

        <Transition name="fade-slide">
          <div v-if="showReplyInput" class="mt-2 flex items-center gap-2">
            <input
              v-model="replyContent"
              @keyup.enter="submitReply"
              placeholder="Write a reply..."
              class="flex-1 bg-white border border-gray-200 rounded-full px-3 py-1.5 text-sm outline-none focus:border-blue-400"
              :disabled="replySubmitting"
            >
            <button
              @click="submitReply"
              type="button"
              :disabled="replySubmitting"
              class="w-8 h-8 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition flex items-center justify-center disabled:cursor-not-allowed disabled:opacity-60"
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
            @changed="$emit('changed')"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useAuthStore } from '@/stores/AuthStore'
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

const emit = defineEmits(['changed'])

const commentStore = useCommentStore()
const authStore = useAuthStore()
const showReplyInput = ref(false)
const showActionsMenu = ref(false)
const isEditing = ref(false)
const editContent = ref('')
const replyContent = ref('')
const replySubmitting = ref(false)
const savingNode = ref(false)
const actionError = ref('')

const author = computed(() => props.node.user || props.node.author || {})
const children = computed(() => props.node.replies || props.node.children || [])
const canReply = computed(() => props.node.node_type !== 'reply')
const canManageNode = computed(() => Boolean(authStore.user?.id && author.value?.id === authStore.user.id))
const emojiMap = {
  LIKE: '👍',
  LOVE: '❤️',
  HAHA: '😂',
  WOW: '😮',
  SAD: '😢',
  GRR: '😠'
}

const reactionKeyMap = {
  like: 'LIKE',
  likes: 'LIKE',
  love: 'LOVE',
  loves: 'LOVE',
  haha: 'HAHA',
  hahas: 'HAHA',
  wow: 'WOW',
  wows: 'WOW',
  sad: 'SAD',
  sads: 'SAD',
  grr: 'GRR',
  grrs: 'GRR'
}

const reactionBreakdown = computed(() => {
  const summary = props.node.reactions_summary || {}

  const groupedCounts = Object.entries(summary).reduce((acc, [key, value]) => {
    if (key === 'total' || key === 'dislikes' || value <= 0) return acc

    const canonicalType = reactionKeyMap[key.toLowerCase()] || key.toUpperCase()
    if (!emojiMap[canonicalType]) return acc

    acc[canonicalType] = (acc[canonicalType] || 0) + value
    return acc
  }, {})

  return Object.entries(groupedCounts)
    .sort((a, b) => b[1] - a[1])
    .slice(0, 3)
    .map(([type]) => ({
      type,
      emoji: emojiMap[type]
    }))
})

const totalReactions = computed(() => props.node.reactions_summary?.total || 0)

const toggleReply = () => {
  showActionsMenu.value = false
  showReplyInput.value = !showReplyInput.value
}

const startEdit = () => {
  editContent.value = props.node.content || ''
  actionError.value = ''
  isEditing.value = true
  showActionsMenu.value = false
  showReplyInput.value = false
}

const cancelEdit = () => {
  isEditing.value = false
  editContent.value = ''
  actionError.value = ''
}

const saveNode = async () => {
  if (!editContent.value.trim() || savingNode.value) return

  savingNode.value = true
  actionError.value = ''

  try {
    if (props.node.node_type === 'reply') {
      await commentStore.updateReply(props.postId, props.node.id, editContent.value.trim())
    } else {
      await commentStore.updateComment(props.postId, props.node.id, editContent.value.trim())
    }

    isEditing.value = false
    emit('changed')
  } catch (error) {
    actionError.value = error?.response?.data?.message || 'Failed to update this item.'
  } finally {
    savingNode.value = false
  }
}

const deleteNode = async () => {
  if (savingNode.value) return

  const confirmed = window.confirm(`Delete this ${props.node.node_type === 'reply' ? 'reply' : 'comment'}?`)
  if (!confirmed) return

  savingNode.value = true
  actionError.value = ''
  showActionsMenu.value = false

  try {
    if (props.node.node_type === 'reply') {
      await commentStore.deleteReply(props.postId, props.node.id)
    } else {
      await commentStore.deleteComment(props.postId, props.node.id)
    }

    emit('changed')
  } catch (error) {
    actionError.value = error?.response?.data?.message || 'Failed to delete this item.'
  } finally {
    savingNode.value = false
  }
}

const submitReply = async () => {
  if (!replyContent.value.trim() || replySubmitting.value) return

  replySubmitting.value = true
  actionError.value = ''

  try {
    await commentStore.addReply(props.postId, props.node.id, replyContent.value)
    replyContent.value = ''
    showReplyInput.value = false
    emit('changed')
  } catch (error) {
    actionError.value = error?.response?.data?.message || 'Failed to add reply.'
  } finally {
    replySubmitting.value = false
  }
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
