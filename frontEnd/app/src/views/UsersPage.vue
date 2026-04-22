<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Users</h1>
    
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="i in 6" :key="i" class="bg-white rounded-2xl p-4 animate-pulse">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-gray-200 rounded w-24"></div>
            <div class="h-3 bg-gray-200 rounded w-16"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="users.length === 0" class="bg-white rounded-2xl p-8 text-center">
      <p class="text-gray-500">No users found</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="user in users"
        :key="user.id"
        @click="viewProfile(user.id)"
        class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition cursor-pointer"
      >
        <div class="flex items-center gap-3">
          <img
            :src="user.profileImage || '/default-avatar.png'"
            class="w-12 h-12 rounded-full object-cover"
          >
          <div class="flex-1 min-w-0">
            <p class="font-bold text-gray-900 truncate">{{ user.name }}</p>
            <p class="text-sm text-gray-500 capitalize">{{ user.role }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userStore'

const router = useRouter()
const userStore = useUserStore()
const users = ref([])
const loading = ref(true)

const fetchUsers = async () => {
  loading.value = true
  try {
    await userStore.fetchUsers()
    users.value = userStore.users
  } catch (error) {
    console.error('Failed to fetch users:', error)
  } finally {
    loading.value = false
  }
}

const viewProfile = (id) => {
  router.push(`/dashboard/profile/${id}`)
}

onMounted(fetchUsers)
</script>