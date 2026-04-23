<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4 overflow-hidden">
    <div class="p-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="relative">
          <img
            :src="post.author.profile_image || '/default-avatar.png'"
            class="w-10 h-10 rounded-full border border-gray-100 object-cover"
            alt="Author avatar"
          >
          <div v-if="post.author.is_online" class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
        </div>
        <div>
          <h4 class="font-bold text-[15px] text-gray-900 leading-tight hover:underline cursor-pointer">
            {{ post.author.name }}
          </h4>
          <p class="text-xs text-gray-500 flex items-center gap-1">
            {{ post.created_at }} • 🌎
          </p>
        </div>
      </div>

      <button class="p-2 hover:bg-gray-100 rounded-full transition text-gray-500">
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
      </button>
    </div>

    <div class="px-4 pb-3 text-[15px] text-gray-800 leading-normal">
      {{ post.content }}
    </div>

    <div v-if="post.media?.length" class="bg-gray-50 border-y border-gray-50">
      <div :class="gridConfig" class="grid gap-[2px]">
        <div
          v-for="(m, i) in post.media.slice(0, 4)"
          :key="i"
          class="relative aspect-square bg-gray-200 overflow-hidden group"
        >
          <video
            v-if="m.mediaType === 'VIDEO'"
            :src="m.url"
            class="w-full h-full object-cover"
            @click="openLightbox(i)"
          ></video>

          <img
            v-else
            :src="m.url"
            class="w-full h-full object-cover cursor-pointer hover:opacity-95 transition"
            @click="openLightbox(i)"
          >

          <div
            v-if="i === 3 && post.media.length > 4"
            class="absolute inset-0 bg-black/60 flex items-center justify-center text-white text-xl font-bold pointer-events-none"
          >
            +{{ post.media.length - 4 }}
          </div>
        </div>
      </div>
    </div>

    <div class="px-4 py-2.5 flex items-center justify-between text-[13px] text-gray-500 border-b border-gray-100">
      <div class="flex items-center gap-1.5">
        <div class="flex -space-x-1">
          <span v-for="emoji in topReactionsEmojis" :key="emoji" class="w-4 h-4 flex items-center justify-center rounded-full bg-white text-[11px] shadow-sm">
            {{ emoji }}
          </span>
        </div>
        <span class="hover:underline cursor-pointer ml-1 font-medium">
          {{ post.reactions_summary?.total || 0 }}
        </span>
      </div>

      <div class="flex gap-3">
        <span @click="toggleComments" class="hover:underline cursor-pointer">
          {{ post.comments_count }} comments
        </span>
        <span class="hover:underline cursor-pointer">12 shares</span>
      </div>
    </div>

    <div class="px-3 py-1 flex items-center gap-1">
      <div class="flex-1">
        <ReactionPicker
          :user-reaction="post.user_reaction"
          @select="handleReaction"
          @toggle="handleToggleLike"
        />
      </div>

      <button
        @click="toggleComments"
        class="flex-1 py-2 flex items-center justify-center gap-2 hover:bg-gray-100 rounded-lg transition-all text-gray-600 font-semibold text-[14px]"
      >
        <span class="text-xl">💬</span>
        <span>Comment</span>
      </button>

      <button class="flex-1 py-2 flex items-center justify-center gap-2 hover:bg-gray-100 rounded-lg transition-all text-gray-600 font-semibold text-[14px]">
        <span class="text-xl">↗️</span>
        <span>Share</span>
      </button>
    </div>

    <Transition name="fade">
      <div v-if="showComments" class="bg-gray-50 border-t border-gray-100">
        <CommentSection :post-id="post.id" />
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePostStore } from '@/stores/PostStore';
import ReactionPicker from './ReactionPicker.vue';
import CommentSection from './CommentSection.vue';

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

// Configures the grid layout based on number of media items
const gridConfig = computed(() => {
  const count = props.post.media?.length || 0;
  if (count === 1) return 'grid-cols-1';
  if (count === 2) return 'grid-cols-2';
  if (count === 3) return 'grid-cols-2 [ &>*:first-child]:row-span-2';
  return 'grid-cols-2';
});

// Extracts the top 3 most used reactions for display
const topReactionsEmojis = computed(() => {
  const summary = props.post.reactions_summary || {};
  return Object.entries(summary)
    .filter(([key, val]) => val > 0 && key !== 'total' && key !== 'dislikes')
    .sort((a, b) => b[1] - a[1])
    .slice(0, 3)
    .map(([key]) => emojiMap[key.toUpperCase()] || '👍');
});

const toggleComments = () => {
  showComments.value = !showComments.value;
};

const handleReaction = (type) => {
  postStore.toggleReaction(props.post.id, type);
};

const handleToggleLike = () => {
  const newType = props.post.user_reaction ? null : 'LIKE';
  postStore.toggleReaction(props.post.id, newType);
};

const openLightbox = (index) => {
  console.log('Open media at index:', index);
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
