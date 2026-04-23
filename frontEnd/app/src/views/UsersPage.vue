<template>
  <div class="space-y-6 p-4">
    <h1 class="text-2xl font-bold text-gray-900">Communauté</h1>

    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="i in 6" :key="i" class="bg-white rounded-2xl p-4 animate-pulse border border-gray-100">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-gray-200 rounded w-24"></div>
            <div class="h-3 bg-gray-200 rounded w-16"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="users.length === 0" class="bg-white rounded-2xl p-12 text-center border shadow-sm">
      <p class="text-gray-500 font-medium">Aucun utilisateur trouvé.</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="user in users"
        :key="user.id"
        @click="viewProfile(user.id)"
        class="group bg-white rounded-2xl p-4 shadow-sm border border-gray-100 hover:border-indigo-300 hover:shadow-md transition-all cursor-pointer"
      >
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-full overflow-hidden bg-gray-100 flex-shrink-0 ring-2 ring-transparent group-hover:ring-indigo-100">
            <img
              v-if="user.profileImage"
              :src="user.profileImage"
              class="w-full h-full object-cover"
            >
            <div v-else class="w-full h-full flex items-center justify-center bg-indigo-50 text-indigo-600 font-bold text-xl uppercase">
              {{ user.name?.charAt(0) }}
            </div>
          </div>

          <div class="flex-1 min-w-0">
            <p class="font-bold text-gray-900 truncate group-hover:text-indigo-600 transition-colors">{{ user.name }}</p>
            <div class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-green-500"></span>
              <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">{{ user.role }}</p>
            </div>
          </div>

          <svg class="w-5 h-5 text-gray-300 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
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
    // T-aked f userStore belli data kat-stocka f users
    // Ghaleb l-amr ghadi tkon response.data.data
    users.value = userStore.users
  } catch (error) {
    console.error('Fetch Error:', error)
  } finally {
    loading.value = false
  }
}

const viewProfile = (id) => {
  // T-aked men had l-path wach s-hih f router dyalk
  router.push(`/dashboard/profile/${id}`)
}

onMounted(fetchUsers)
</script>
