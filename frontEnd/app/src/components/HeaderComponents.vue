<template>
  <header class="w-full bg-white border-b border-gray-200 h-20 px-6 md:px-12 flex items-center justify-between shadow-sm shrink-0 z-50">
    <div class="flex items-center gap-16 flex-1">
      <RouterLink to="/" class="text-3xl font-black text-[#064E3B] tracking-tighter cursor-pointer no-underline">
        SportLink
      </RouterLink>

      <div class="hidden md:block relative w-full max-w-2xl">
        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Rechercher des athlètes..."
          class="w-full bg-gray-100 border-none rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-[#064E3B] outline-none transition-all"
          @keyup.enter="goSearch"
        >
      </div>
    </div>

    <div class="flex items-center gap-6 ml-8">
      <button class="relative text-gray-500 hover:text-[#064E3B] transition-colors">
        <i class="fa-solid fa-bell text-2xl"></i>
        <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full border-2 border-white"></span>
      </button>

      <!-- Profile dropdown -->
      <div class="relative">
        <button
          @click="showDropdown = !showDropdown"
          class="h-11 w-11 rounded-full border-2 border-gray-200 overflow-hidden cursor-pointer hover:border-[#064E3B] transition-colors"
        >
          <img
            :src="user?.profileImage || `https://ui-avatars.com/api/?name=${encodeURIComponent(user?.name || 'User')}&bg=064E3B&color=fff`"
            alt="User Profile"
            class="w-full h-full object-cover"
          >
        </button>

        <div
          v-if="showDropdown"
          class="absolute right-0 top-14 bg-white rounded-2xl border border-gray-100 shadow-xl z-50 overflow-hidden min-w-[200px]"
        >
          <div class="px-4 py-3 border-b border-gray-50">
            <p class="font-bold text-gray-900 text-sm">{{ user?.name }}</p>
            <p class="text-xs text-gray-400">{{ user?.email }}</p>
          </div>
          <RouterLink
            :to="`/users/${user?.id}`"
            class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 font-medium no-underline"
            @click="showDropdown = false"
          >
            <i class="fa-regular fa-user text-emerald-600"></i> Mon profil
          </RouterLink>
          <RouterLink
            to="/settings"
            class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 font-medium no-underline"
            @click="showDropdown = false"
          >
            <i class="fa-solid fa-gear text-gray-400"></i> Paramètres
          </RouterLink>
          <button
            @click="handleLogout"
            class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 font-medium border-t border-gray-50"
          >
            <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
          </button>
        </div>
        <div v-if="showDropdown" class="fixed inset-0 z-40" @click="showDropdown = false"></div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/AuthStore'
import { storeToRefs } from 'pinia'

const authStore = useAuthStore()
const { user } = storeToRefs(authStore)
const router = useRouter()

const searchQuery = ref('')
const showDropdown = ref(false)

const goSearch = () => {
  if (searchQuery.value.trim()) {
    router.push(`/search?q=${encodeURIComponent(searchQuery.value.trim())}`)
    searchQuery.value = ''
  }
}

const handleLogout = () => {
  showDropdown.value = false
  authStore.logout()
}
</script>
