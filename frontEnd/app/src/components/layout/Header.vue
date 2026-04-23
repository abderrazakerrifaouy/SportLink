<template>
  <nav class="bg-white border-b border-gray-100 sticky top-0 z-1000 shadow-sm">
    <div class="max-width-screen-xl mx-auto px-4">
      <div class="flex justify-between items-center h-16">

        <div class="flex items-center flex-1 space-x-6">
          <router-link to="/dashboard" class="shrink-0">
            <span class="text-2xl font-black bg-linear-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
              SPORT LIK
            </span>
          </router-link>

          <div class="hidden md:block w-full max-w-sm relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Rechercher un joueur, un club..."
              class="block w-full bg-gray-50 border border-transparent rounded-2xl py-2.5 pl-10 pr-3 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 outline-none"
            >

            <transition name="fade">
              <div v-if="searchResults && searchResults.length > 0"
                class="absolute mt-3 w-[110%] -left-[5%] bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-gray-100 overflow-hidden backdrop-blur-lg">

                <div class="p-2">
                  <p class="text-[11px] font-bold text-gray-400 px-4 py-2 uppercase tracking-wider">Résultats trouvés</p>

                  <div
                    v-for="item in searchResults"
                    :key="item.id"
                    @click="navigateToProfile(item)"
                    class="group flex items-center p-3 hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent rounded-2xl cursor-pointer transition-all duration-200 mb-1 last:mb-0"
                  >
                    <div class="relative">
                      <img :src="item.profileImage || '/default-avatar.png'"
                           class="w-11 h-11 rounded-full object-cover border-2 border-transparent group-hover:border-blue-500 transition-all shadow-sm">
                      <div v-if="item.isVerified" class="absolute -right-1 -bottom-1 bg-blue-500 text-white p-0.5 rounded-full border-2 border-white">
                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293l-4 4a1 1 0 01-1.414 0l-2-2a1 1 0 111.414-1.414L9 10.586l3.293-3.293a1 1 0 011.414 1.414z"></path></svg>
                      </div>
                    </div>

                    <div class="ml-4 flex-1">
                      <p class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ item.name }}</p>
                      <div class="flex items-center space-x-2">
                        <span class="text-[10px] px-2 py-0.5 bg-blue-100 text-blue-600 rounded-md font-bold uppercase">{{ item.role }}</span>
                        <span v-if="item.location" class="text-[10px] text-gray-400 font-medium tracking-tight">
                           • {{ item.location }}
                        </span>
                      </div>
                    </div>

                    <div class="opacity-0 group-hover:opacity-100 transition-opacity pr-2">
                      <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                      </svg>
                    </div>
                  </div>
                </div>

                <div class="bg-gray-50 p-3 text-center">
                   <button class="text-xs font-bold text-blue-600 hover:underline">Voir tous les résultats</button>
                </div>
              </div>
            </transition>
          </div>
        </div>

        <div class="flex items-center space-x-4">
          <div v-if="auth.user" class="flex items-center space-x-3 bg-gray-50 p-1.5 pr-4 rounded-2xl border border-gray-100 hover:shadow-md transition-shadow">
            <div class="relative">
              <img
                :src="auth.user.profileImage || '/default-avatar.png'"
                class="w-9 h-9 rounded-full object-cover border-2 border-white shadow-sm"
              >
              <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-400 ring-2 ring-white"></span>
            </div>

            <div class="hidden sm:block">
              <p class="text-sm font-bold text-gray-900 leading-tight">{{ auth.user.name }}</p>
              <div class="flex space-x-2 text-[10px] text-gray-500 font-medium">
                <span class="text-blue-600">Pro Profil</span>
                <span>•</span>
                <span class="uppercase">{{ auth.user.role }}</span>
              </div>
            </div>
          </div>

          <button
            @click="auth.logout"
            class="bg-white border border-gray-200 hover:border-red-200 hover:text-red-600 text-gray-500 p-2.5 rounded-2xl transition-all shadow-sm active:scale-95"
            title="Déconnexion"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </button>
        </div>

      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useAuthStore } from '@/stores/AuthStore'
import { useUserStore  } from '@/stores/userStore'
import { useRouter } from 'vue-router'
import debounce from 'lodash/debounce'

const auth = useAuthStore()
const userStore = useUserStore()
const router = useRouter()
const searchQuery = ref('')
const searchResults = ref([])


const fetchResults = debounce(async (query) => {
  if (!query || query.trim() === '') {
    searchResults.value = []
    return
  }
  try {
    const data = await userStore.searchUsers(query)
    searchResults.value = data || []
  } catch (error) {
    console.error("Search failed:", error)
    searchResults.value = []
  }
}, 300)

watch(searchQuery, (newVal) => fetchResults(newVal))

const navigateToProfile = (user) => {
  searchQuery.value = ''
  searchResults.value = []
  const role = user.role?.toLowerCase()
  const path = '/dashboard/profile/' + user.id
  router.push(path)
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: all 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
