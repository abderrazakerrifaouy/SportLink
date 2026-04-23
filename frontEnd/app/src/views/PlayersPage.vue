<template>
  <div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <h1 class="text-2xl font-bold text-gray-900">Players List</h1>
      <div class="relative">
        <input
          v-model="searchInput"
          @input="onSearchInput"
          type="text"
          placeholder="Search by name, position, club..."
          class="w-64 md:w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
        />
        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>
    </div>

    <div v-if="loading" class="flex flex-col items-center justify-center py-12">
      <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600 mb-4"></div>
      <p class="text-gray-500">Chargement des joueurs...</p>
    </div>

    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-700 flex items-center">
      <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
      </svg>
      {{ error }}
    </div>

    <div v-else-if="filteredPlayers.length === 0" class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100">
      <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900">Aucun joueur trouvé</h3>
      <p class="text-gray-500 mt-1">{{ searchInput ? 'Essayez un autre mot-clé.' : 'La liste est vide pour le moment.' }}</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="player in filteredPlayers"
        :key="player.id"
        @click="goToPlayerDetails(player.id)"
        class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 cursor-pointer"
      >
        <div class="flex items-center space-x-4">
          <div class="w-14 h-14 rounded-full bg-indigo-50 flex items-center justify-center flex-shrink-0 overflow-hidden ring-2 ring-transparent group-hover:ring-indigo-100 transition-all">
            <img
              :src="player.user?.profileImage || '/default-avatar.png'"
              class="w-full h-full object-cover"
              @error="(e) => e.target.style.display = 'none'"
            />
          </div>

          <div class="flex-1 min-w-0">
            <h3 class="text-gray-900 font-bold truncate group-hover:text-indigo-600 transition-colors">
              {{ player.user?.name }}
            </h3>
            <p class="text-xs text-gray-500 truncate">{{ player.user?.email }}</p>
          </div>
        </div>

        <div class="mt-5 flex items-center justify-between">
          <div class="flex items-center text-xs font-medium text-gray-600 bg-gray-50 px-2.5 py-1 rounded-lg">
            <svg class="w-3.5 h-3.5 mr-1.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 001.75-2.75L16.5 14.5a2 2 0 00-2-2H7.5a2 2 0 00-2 2L3 17.25A2 2 0 005 20z" />
            </svg>
            {{ player.experiences?.length || 0 }} Expérience{{ player.experiences?.length !== 1 ? 's' : '' }}
          </div>

          <span class="text-indigo-600 text-xs font-semibold flex items-center opacity-0 group-hover:opacity-100 transition-opacity">
            Voir Profil
            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useJoueurStore } from '@/stores/joueurStore'

const router = useRouter()
const joueurStore = useJoueurStore()

const loading = ref(true)
const error = ref(null)
const searchInput = ref('')

const filteredPlayers = computed(() => {
  return joueurStore.filteredJoueurs
})

const onSearchInput = () => {
  joueurStore.setSearchQuery(searchInput.value)
}

const fetchPlayers = async () => {
  loading.value = true
  error.value = null
  try {
    await joueurStore.fetchJoueurs()
  } catch (err) {
    error.value = "Impossible de charger les joueurs. Veuillez réessayer."
    console.error("Fetch Error:", err)
  } finally {
    loading.value = false
  }
}

const goToPlayerDetails = (playerId) => {
  router.push({ name: 'PlayerDetails', params: { id: playerId } })
}

onMounted(() => {
  fetchPlayers()
})
</script>
