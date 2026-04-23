<template>
  <div class="space-y-4">
    <div class="flex gap-2">
      <input
        v-model="newComment"
        @keyup.enter="postComment"
        placeholder="Write a comment..."
        class="flex-1 bg-white border border-gray-200 rounded-full px-4 py-1.5 text-sm outline-none focus:border-blue-400"
      >
    </div>

    <div v-for="c in comments" :key="c.id" class="flex gap-2">
      <img :src="c.user.profile_image || '/default-avatar.png'" class="w-8 h-8 rounded-full border">
      <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 max-w-[90%]">
        <p class="text-xs font-bold text-gray-900">{{ c.user.name }}</p>
        <p class="text-sm text-gray-700 mt-1">{{ c.content }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useCommentStore } from '@/stores/commentStore';

const props = defineProps({ postId: Number });
const commentStore = useCommentStore();
const newComment = ref('');

const comments = computed(() => commentStore.getComments(props.postId));

onMounted(() => commentStore.fetchComments(props.postId));

const postComment = async () => {
  if (!newComment.value.trim()) return;
  await commentStore.addComment(props.postId, newComment.value);
  newComment.value = '';
};
</script>
