<template>
  <article class="mb-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm ring-1 ring-slate-900/5">
    <header class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
      <div class="flex items-center gap-3">
        <div class="relative">
          <img
            :src="post.author.profile_image || '/default-avatar.png'"
            class="h-11 w-11 rounded-full border-2 border-white object-cover shadow"
            alt="Author avatar"
          >
          <div
            v-if="post.author.is_online"
            class="absolute -bottom-0.5 -right-0.5 h-3.5 w-3.5 rounded-full border-2 border-white bg-emerald-500"
          ></div>
        </div>

        <div>
          <h4 class="text-[15px] font-extrabold tracking-tight text-slate-900">
            {{ post.author.name }}
          </h4>
          <p class="mt-0.5 flex items-center gap-1 text-xs text-slate-500">
            {{ post.created_at }}
            <span>•</span>
            <span class="text-[10px]">Public</span>
          </p>
        </div>
      </div>

      <div v-if="canManagePost" class="relative">
        <button
          type="button"
          class="rounded-full p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
          @click.stop="showPostMenu = !showPostMenu"
        >
          <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
        </button>

        <div v-if="showPostMenu" class="absolute right-0 z-20 mt-2 w-36 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
          <button
            type="button"
            class="block w-full px-4 py-2.5 text-left text-sm font-semibold text-slate-700 hover:bg-slate-50"
            @click="startEditPost"
          >
            Edit
          </button>
          <button
            type="button"
            class="block w-full px-4 py-2.5 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50"
            @click="confirmDeletePost"
          >
            Delete
          </button>
        </div>
      </div>
    </header>

    <section class="px-5 pt-4 pb-2">
      <div v-if="isEditing" class="space-y-3">
        <textarea
          v-model="editContent"
          rows="4"
          class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-[15px] leading-7 text-slate-700 outline-none focus:border-indigo-400"
        ></textarea>
        <div class="flex flex-wrap gap-2">
          <button
            type="button"
            class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="savingPost"
            @click="savePost"
          >
            {{ savingPost ? 'Saving...' : 'Save' }}
          </button>
          <button
            type="button"
            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300"
            :disabled="savingPost"
            @click="cancelEditPost"
          >
            Cancel
          </button>
        </div>
      </div>

      <p v-else class="whitespace-pre-line text-[15px] leading-7 text-slate-700">
        {{ post.content }}
      </p>

      <PostMediaDisplay v-if="post.media?.length" :media="post.media" />

      <p v-if="actionError" class="mt-3 text-sm font-medium text-rose-600">
        {{ actionError }}
      </p>
    </section>

    <section class="mt-2 border-t border-slate-100 px-5 py-3">
      <div class="flex items-center justify-between text-[13px] text-slate-500">
        <div class="flex items-center gap-2">
          <div class="flex items-center gap-1.5">
            <span
              v-for="item in reactionBreakdown"
              :key="item.type"
              class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-50 text-xs shadow-sm ring-1 ring-slate-200"
            >
              {{ item.emoji }}
            </span>
          </div>
          <span class="font-semibold text-slate-600">{{ post.reactions_summary?.total || 0 }}</span>
        </div>

        <div class="flex items-center gap-4">
          <button type="button" @click="toggleComments" class="font-medium transition hover:text-slate-700 hover:underline">
            {{ post.comments_count }} comments
          </button>
          <span class="font-medium text-slate-400">Share</span>
        </div>
      </div>
    </section>

    <section class="border-t border-slate-100 px-3 py-2">
      <div class="grid grid-cols-3 gap-1.5">
        <div class="rounded-xl px-1 py-1 transition hover:bg-slate-100">
          <ReactionPicker
            :user-reaction="post.user_reaction"
            @select="handleReaction"
            @toggle="handleToggleLike"
          />
        </div>

        <button
          type="button"
          @click="toggleComments"
          class="inline-flex items-center justify-center gap-2 rounded-xl py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
        >
          <span>💬</span>
          <span>Comment</span>
        </button>

        <button
          type="button"
          class="inline-flex items-center justify-center gap-2 rounded-xl py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
        >
          <span>↗</span>
          <span>Share</span>
        </button>
      </div>
    </section>

    <Transition name="fade">
      <div v-if="showComments" class="border-t border-slate-100 bg-slate-50/70">
        <CommentSection :post-id="post.id" @changed="handleCommentsChanged" />
      </div>
    </Transition>
  </article>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '@/stores/AuthStore';
import { usePostStore } from '@/stores/PostStore';
import ReactionPicker from './ReactionPicker.vue';
import CommentSection from './CommentSection.vue';
import PostMediaDisplay from './PostMediaDisplay.vue';

const props = defineProps({
  post: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['deleted', 'post-mutated']);

const postStore = usePostStore();
const authStore = useAuthStore();
const showComments = ref(false);
const showPostMenu = ref(false);
const isEditing = ref(false);
const editContent = ref('');
const savingPost = ref(false);
const deletingPost = ref(false);
const actionError = ref('');

const canManagePost = computed(() => {
  return Boolean(authStore.user?.id && props.post.author?.id === authStore.user.id)
});

// Emoji mapping for top reactions display
const emojiMap = {
  LIKE: '👍',
  LOVE: '❤️',
  HAHA: '😂',
  WOW: '😮',
  SAD: '😢',
  GRR: '😠'
};

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
};

const reactionBreakdown = computed(() => {
  const summary = props.post.reactions_summary || {};

  const groupedCounts = Object.entries(summary).reduce((acc, [key, value]) => {
    if (key === 'total' || key === 'dislikes' || value <= 0) return acc;

    const canonicalType = reactionKeyMap[key.toLowerCase()] || key.toUpperCase();
    if (!emojiMap[canonicalType]) return acc;

    acc[canonicalType] = (acc[canonicalType] || 0) + value;
    return acc;
  }, {});

  return Object.entries(groupedCounts)
    .sort((a, b) => b[1] - a[1])
    .slice(0, 3)
    .map(([type]) => ({
      type,
      emoji: emojiMap[type]
    }));
});

const toggleComments = () => {
  showPostMenu.value = false;
  showComments.value = !showComments.value;
};

const handleCommentsChanged = () => {
  emit('post-mutated');
};

const startEditPost = () => {
  editContent.value = props.post.content || '';
  actionError.value = '';
  isEditing.value = true;
  showPostMenu.value = false;
};

const cancelEditPost = () => {
  isEditing.value = false;
  editContent.value = '';
  actionError.value = '';
};

const savePost = async () => {
  if (!editContent.value.trim() || savingPost.value) return;

  savingPost.value = true;
  actionError.value = '';

  try {
    await postStore.updatePost(props.post.id, { content: editContent.value.trim() });
    isEditing.value = false;
    emit('post-mutated');
  } catch (error) {
    actionError.value = error?.response?.data?.message || 'Failed to update post.';
  } finally {
    savingPost.value = false;
  }
};

const confirmDeletePost = async () => {
  if (deletingPost.value) return;

  const confirmed = window.confirm('Delete this post?');
  if (!confirmed) return;

  deletingPost.value = true;
  actionError.value = '';
  showPostMenu.value = false;

  try {
    await postStore.deletePost(props.post.id);
    emit('deleted');
    emit('post-mutated');
  } catch (error) {
    actionError.value = error?.response?.data?.message || 'Failed to delete post.';
  } finally {
    deletingPost.value = false;
  }
};

const handleReaction = (type) => {
  postStore.toggleReaction(props.post.id, type);
};

const handleToggleLike = () => {
  const selectedType = props.post.user_reaction || 'LIKE';
  postStore.toggleReaction(props.post.id, selectedType);
};

</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.grid-cols-2 > *:first-child.row-span-2 {
  grid-row: span 2 / span 2;
  aspect-ratio: auto;
}
</style>
