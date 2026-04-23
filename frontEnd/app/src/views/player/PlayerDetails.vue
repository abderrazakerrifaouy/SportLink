<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useJoueurStore } from '@/stores/joueurStore'

const route = useRoute()
const joueurStore = useJoueurStore()
const loading = ref(true)
const error = ref(null)

// Hna l-fix: l-player ghadi y-khoud data direct
const player = ref(null)

const sortedExperiences = computed(() => {
  // Kan-chekkiw wach experiences kayna w fiha l-data
  const experiences = player.value?.experiences || []
  return [...experiences].sort((a, b) => new Date(b.joinDate) - new Date(a.joinDate))
})

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-GB', { year: 'numeric', month: 'short' }) : ''

const categoryClass = (c) => {
  const map = {
    PROFESSIONAL: 'bg-green-100 text-green-700',
    SENIOR: 'bg-red-100 text-red-700',
    JUNIOR: 'bg-blue-100 text-blue-700'
  }
  return map[c?.toUpperCase()] || 'bg-gray-100 text-gray-600'
}

const fetchPlayer = async () => {
  loading.value = true
  error.value = null
  try {
    player.value = (await joueurStore.fetchJoueur(route.params.id)).data
    console.log("Fetched Player Data:", player.value)
  }
  catch (err) {
    error.value = "Failed to load player data."
    console.error("Fetch Error:", err)
  }
  finally {
    loading.value = false
  }
}

onMounted(fetchPlayer)
</script>

<template>
  <div class="max-w-4xl mx-auto space-y-6 pb-12">
    <div v-if="loading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600"></div>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-3xl p-6 text-red-700 text-center">
      <p class="font-bold">{{ error }}</p>
      <button @click="fetchPlayer" class="mt-4 text-indigo-600 underline font-medium">Try again</button>
    </div>

    <div v-else-if="player" class="space-y-6">
      <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-gray-100">
        <div class="h-40 bg-indigo-600 relative overflow-hidden">
          <img v-if="player.user?.bannerImage" :src="player.user.bannerImage" class="w-full h-full object-cover opacity-60" />
          <div v-else class="w-full h-full bg-linear-to-r from-indigo-600 to-purple-600"></div>
        </div>

        <div class="px-6 pb-6">
          <div class="relative flex justify-between items-end -mt-12 mb-4">
            <div class="p-1 bg-white rounded-full shadow-lg">
              <div class="w-24 h-24 rounded-full bg-indigo-50 border-4 border-white overflow-hidden flex items-center justify-center">
                <img v-if="player.user?.profileImage" :src="player.user.profileImage" class="w-full h-full object-cover" />
                <span v-else class="text-3xl font-black text-indigo-500">{{ player.user?.name?.charAt(0) }}</span>
              </div>
            </div>

            <div class="flex gap-4 mb-2">
              <div class="text-center px-2">
                <p class="font-black text-gray-900 leading-none">{{ player.user?.stats?.followers_count || 0 }}</p>
                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mt-1">Followers</p>
              </div>
              <div class="text-center px-2 border-x border-gray-100">
                <p class="font-black text-gray-900 leading-none">{{ player.user?.stats?.following_count || 0 }}</p>
                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mt-1">Following</p>
              </div>
            </div>
          </div>

          <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{ player.user?.name }}</h1>
            <p class="text-indigo-600 font-bold text-sm uppercase tracking-wide">{{ player.user?.role }}</p>
            <p v-if="player.user?.bio" class="mt-3 text-gray-600 text-sm leading-relaxed max-w-2xl">{{ player.user.bio }}</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1 space-y-6">
          <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-black text-xs text-gray-400 uppercase tracking-[0.2em] mb-4">Player Info</h3>
            <div class="space-y-4">
              <div class="flex items-center text-gray-600">
                <svg class="w-5 h-5 mr-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span class="text-sm font-medium truncate">{{ player.user?.email }}</span>
              </div>
              <div class="flex items-center text-gray-600">
                <svg class="w-5 h-5 mr-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span class="text-sm font-medium">Joined {{ formatDate(player.user?.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="md:col-span-2">
          <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-8">
              <h2 class="text-xl font-black text-gray-900">Career Timeline</h2>
              <span class="bg-indigo-50 text-indigo-700 px-4 py-1 rounded-full text-xs font-black uppercase">{{ sortedExperiences.length }} Clubs</span>
            </div>

            <div v-if="sortedExperiences.length" class="relative pl-2">
              <div class="absolute left-6 top-2 bottom-2 w-0.5 bg-gray-100"></div>
              <div class="space-y-10">
                <div v-for="exp in sortedExperiences" :key="exp.id" class="relative pl-12">
                  <div class="absolute left-4 top-1.5 w-4 h-4 rounded-full bg-white border-4 border-indigo-600 shadow-sm z-10"></div>
                  <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2">
                    <div>
                      <h3 class="font-black text-gray-900 text-lg leading-tight">{{ exp.nomClub }}</h3>
                      <p class="text-gray-500 text-sm font-bold mt-1 uppercase tracking-tighter">
                        {{ formatDate(exp.joinDate) }} — {{ exp.endDate ? formatDate(exp.endDate) : 'Present' }}
                      </p>
                      <div v-if="exp.place" class="flex items-center mt-2 text-gray-400 text-xs font-bold uppercase tracking-widest">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/></svg>
                        {{ exp.place }}
                      </div>
                    </div>
                    <span :class="categoryClass(exp.categoryType)" class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest self-start">
                      {{ exp.categoryType }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-12 text-gray-400 font-medium italic">No career experiences recorded.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
