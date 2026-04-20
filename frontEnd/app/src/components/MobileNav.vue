<template>
  <nav class="fixed bottom-4 left-4 right-4 bg-white/80 backdrop-blur-xl border border-slate-200/50 shadow-2xl rounded-3xl z-100 px-2 py-3 sm:hidden">
    <div class="flex justify-around items-center">
      <router-link
        to="/explore"
        class="flex flex-col items-center space-y-1 transition-all duration-300"
        v-slot="{ isActive }"
      >
        <div
          class="p-2 rounded-2xl transition-all duration-300"
          :class="isActive ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30 -translate-y-1' : 'text-slate-400'"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <span class="text-[9px] font-black uppercase tracking-widest" :class="isActive ? 'text-blue-600' : 'text-slate-400'">Explore</span>
      </router-link>

      <router-link
        to="/events"
        class="flex flex-col items-center space-y-1 transition-all duration-300"
        v-slot="{ isActive }"
      >
        <div
          class="p-2 rounded-2xl transition-all duration-300"
          :class="isActive ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30 -translate-y-1' : 'text-slate-400'"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z" />
          </svg>
        </div>
        <span class="text-[9px] font-black uppercase tracking-widest" :class="isActive ? 'text-blue-600' : 'text-slate-400'">Events</span>
      </router-link>

      <router-link
        to="/dashboard"
        class="flex flex-col items-center space-y-1 transition-all duration-300"
        v-slot="{ isActive }"
      >
        <div
          class="p-2 rounded-2xl transition-all duration-300"
          :class="isActive ? 'bg-slate-900 text-white shadow-lg -translate-y-1' : 'text-slate-400'"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
          </svg>
        </div>
        <span class="text-[9px] font-black uppercase tracking-widest" :class="isActive ? 'text-slate-900' : 'text-slate-400'">Home</span>
      </router-link>

      <router-link
        to="/profile"
        class="flex flex-col items-center space-y-1 transition-all duration-300"
        v-slot="{ isActive }"
      >
        <div
          class="p-0.5 rounded-full border-2 transition-all duration-300"
          :class="isActive ? 'border-blue-600 scale-110 shadow-lg' : 'border-transparent'"
        >
          <img
            :src="authStore.user?.profile_image || 'https://ui-avatars.com/api/?name=User'"
            class="w-7 h-7 rounded-full object-cover"
            alt="Profile"
          />
        </div>
        <span class="text-[9px] font-black uppercase tracking-widest" :class="isActive ? 'text-blue-600' : 'text-slate-400'">Profil</span>
      </router-link>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '@/stores/AuthStore'
const authStore = useAuthStore()
</script>

<style scoped>
.router-link-active {
  animation: tap-bounce 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes tap-bounce {
  0% { transform: scale(1); }
  50% { transform: scale(0.9); }
  100% { transform: scale(1); }
}
</style>
