<template>
  <aside class="fixed right-0 top-16 bottom-0 w-72 bg-white border-l border-gray-100 overflow-y-auto hidden xl:block">
    <div class="p-4">
      <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">
        Your Followers
      </h3>

      <div v-if="loading" class="space-y-3">
        <div v-for="i in 3" :key="i" class="flex items-center gap-3 animate-pulse">
          <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
          <div class="flex-1 space-y-2">
            <div class="h-3 bg-gray-200 rounded w-24"></div>
            <div class="h-2 bg-gray-200 rounded w-16"></div>
          </div>
        </div>
      </div>

      <div v-else-if="followers.length === 0" class="text-center py-8">
        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <p class="text-xs text-gray-400">No followers yet</p>
      </div>

      <div v-else class="space-y-3">
        <div
          v-for="user in followers"
          :key="user.id"
          class="flex items-center gap-3 p-2 rounded-xl hover:bg-gray-50 cursor-pointer transition-colors"
          @click="navigateToProfile(user)"
        >
          <div class="relative">
            <img
              :src="user.profileImage || '/default-avatar.png'"
              class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm"
            >
            <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-400 ring-2 ring-white"></span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-gray-900 truncate">{{ user.name }}</p>
            <p class="text-xs text-gray-500 capitalize truncate">{{ user.role }}</p>
          </div>
          <button
            @click.stop="toggleFollow(user)"
            class="px-3 py-1 text-xs font-bold rounded-full transition-colors"
            :class="isFollowing(user.id) 
              ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' 
              : 'bg-blue-600 text-white hover:bg-blue-700'"
          >
            {{ isFollowing(user.id) ? 'Following' : 'Follow' }}
          </button>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import { useAuthStore } from '@/stores/AuthStore'

const router = useRouter()
const userStore = useUserStore()
const authStore = useAuthStore()

const loading = ref(false)
const followingIds = ref([])

const auth = computed(() => authStore)
const followers = computed(() => userStore.followers)

onMounted(async () => {
  if (authStore.user?.id) {
    await fetchFollowers()
    await fetchFollowing()
  }
})

watch(() => authStore.user?.id, async (newId) => {
  if (newId) {
    await fetchFollowers()
    await fetchFollowing()
  }
})

const fetchFollowers = async () => {
  if (!authStore.user?.id) return
  loading.value = true
  try {
    await userStore.fetchFollowers(authStore.user.id)
  } catch (error) {
    console.error('Failed to fetch followers:', error)
  } finally {
    loading.value = false
  }
}

const fetchFollowing = async () => {
  if (!authStore.user?.id) return
  try {
    await userStore.fetchFollowing(authStore.user.id)
    followingIds.value = userStore.following.map(f => f.id)
  } catch (error) {
    console.error('Failed to fetch following:', error)
  }
}

const isFollowing = (userId) => {
  return followingIds.value.includes(userId)
}

const toggleFollow = async (user) => {
  try {
    if (isFollowing(user.id)) {
      await userStore.unfollow(user.id)
      followingIds.value = followingIds.value.filter(id => id !== user.id)
    } else {
      await userStore.follow(user.id)
      followingIds.value.push(user.id)
    }
  } catch (error) {
    console.error('Failed to toggle follow:', error)
  }
}

const navigateToProfile = (user) => {
  const role = user.role?.toLowerCase()
  if (role === 'joueur' || role === 'player') {
    router.push(`/players/${user.id}`)
  } else if (role === 'club_admin' || role === 'club admin') {
    router.push(`/clubs/${user.id}`)
  } else {
    router.push(`/users/${user.id}`)
  }
}
</script>