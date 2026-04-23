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

      <button type="button" class="rounded-full p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700">
        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
      </button>
    </header>

    <section class="px-5 pt-4 pb-2">
      <p class="whitespace-pre-line text-[15px] leading-7 text-slate-700">
        {{ post.content }}
      </p>

      <PostMediaDisplay v-if="post.media?.length" :media="post.media" />
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
        <CommentSection :post-id="post.id" />
      </div>
    </Transition>
  </article>
</template>

<script setup>
import { ref, computed } from 'vue';
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

const postStore = usePostStore();
const showComments = ref(false);

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
  showComments.value = !showComments.value;
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
