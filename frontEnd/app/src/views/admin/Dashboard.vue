<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <p class="text-3xl font-bold text-blue-600">{{ stats.totalUsers }}</p>
        <p class="text-gray-500">Total Users</p>
      </div>
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <p class="text-3xl font-bold text-green-600">{{ stats.totalPosts }}</p>
        <p class="text-gray-500">Total Posts</p>
      </div>
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <p class="text-3xl font-bold text-purple-600">{{ stats.totalPlayers }}</p>
        <p class="text-gray-500">Players</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/AuthStore'
import { useUserStore } from '@/stores/userStore'
import { usePostStore } from '@/stores/PostStore'

const authStore = useAuthStore()
const userStore = useUserStore()
const postStore = usePostStore()

const stats = ref({
  totalUsers: 0,
  totalPosts: 0,
  totalPlayers: 0
})

const fetchStats = async () => {
  try {
    await userStore.fetchUsers()
    stats.value.totalUsers = userStore.users.length
    await postStore.fetchPosts()
    stats.value.totalPosts = postStore.posts.length
  } catch (error) {
    console.error('Failed to fetch stats:', error)
  }
}

onMounted(fetchStats)
</script>