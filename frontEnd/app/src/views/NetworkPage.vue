<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">My Network</h1>

    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    </div>

    <template v-else>
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Following ({{ following.length }})</h2>
        <div v-if="following.length === 0" class="text-center py-8 text-gray-400">
          You're not following anyone yet
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="user in following"
            :key="user.id"
            @click="viewProfile(user.id)"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition cursor-pointer border border-transparent hover:border-gray-100"
          >
            <img :src="user.profileImage || '/default-avatar.png'" class="w-12 h-12 rounded-full object-cover shadow-sm">
            <div class="flex-1 min-w-0">
              <p class="font-bold text-gray-900 truncate">{{ user.name }}</p>
              <p class="text-sm text-gray-500 capitalize">{{ user.role }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Followers ({{ followers.length }})</h2>
        <div v-if="followers.length === 0" class="text-center py-8 text-gray-400">
          No followers yet
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="user in followers"
            :key="user.id"
            @click="viewProfile(user.id)"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition cursor-pointer border border-transparent hover:border-gray-100"
          >
            <img :src="user.profileImage || '/default-avatar.png'" class="w-12 h-12 rounded-full object-cover shadow-sm">
            <div class="flex-1 min-w-0">
              <p class="font-bold text-gray-900 truncate">{{ user.name }}</p>
              <p class="text-sm text-gray-500 capitalize">{{ user.role }}</p>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/AuthStore'
import { useUserStore } from '@/stores/userStore'

const router = useRouter()
const authStore = useAuthStore()
const userStore = useUserStore()

const following = ref([])
const followers = ref([])
const loading = ref(true)

const fetchNetwork = async () => {
  // فاش كادير ريفريش، authStore.user يقدر يكون مازال ما وجدش
  // داكشي علاش خاصنا نتأكدوا من الـ ID
  const userId = authStore.user?.id

  if (!userId) {
    // إيلا ما كاينش ID، كنعاودو نطلبوا بيانات المستخدم الحالي
    await authStore.fetchUser()
  }

  const currentId = authStore.user?.id
  if (!currentId) return

  loading.value = true
  try {
    await Promise.all([
      userStore.fetchFollowing(currentId),
      userStore.fetchFollowers(currentId)
    ])

   following.value = userStore.following?.map((item) => {
      return item.following_id
    }) || []

    followers.value = userStore.followers?.map((item) => {
      return item.follower_id
    }) || []

  } catch (error) {
    console.error('Failed to fetch network:', error)
  } finally {
    loading.value = false
  }
}

const viewProfile = (id) => {
  router.push(`/dashboard/profile/${id}`)
}

watch(() => authStore.user?.id, (newId) => {
  if (newId) {
    fetchNetwork()
  }
}, { immediate: true })

onMounted(() => {
  if (authStore.user?.id) {
    fetchNetwork()
  }
})
</script>
