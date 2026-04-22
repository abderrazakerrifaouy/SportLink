<template>
  <div class="space-y-6">
    <CreatePost @created="refreshPosts" />

    <div v-if="postStore.loading && postStore.posts.length === 0" class="space-y-4">
      <div v-for="i in 3" :key="i" class="bg-white rounded-2xl p-4 animate-pulse">
        <div class="flex gap-3 mb-4">
          <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
            <div class="h-3 bg-gray-200 rounded w-1/4"></div>
          </div>
        </div>
        <div class="space-y-2">
          <div class="h-4 bg-gray-200 rounded"></div>
          <div class="h-4 bg-gray-200 rounded w-5/6"></div>
        </div>
      </div>
    </div>

    <div v-else-if="postStore.posts.length === 0" class="bg-white rounded-2xl p-8 text-center">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
      </svg>
      <p class="text-gray-500 font-medium">No posts yet</p>
      <p class="text-gray-400 text-sm mt-1">Be the first to share something!</p>
    </div>

    <div v-else class="space-y-4">
      <PostCard
        v-for="post in postStore.posts"
        :key="post.id"
        :post="post"
        @deleted="refreshPosts"
      />
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { usePostStore } from '@/stores/PostStore'
import CreatePost from '@/components/posts/CreatePost.vue'
import PostCard from '@/components/posts/PostCard.vue'

const postStore = usePostStore()

onMounted(async () => {
  await postStore.fetchPosts()
})

const refreshPosts = async () => {
  await postStore.fetchPosts()
}
</script>