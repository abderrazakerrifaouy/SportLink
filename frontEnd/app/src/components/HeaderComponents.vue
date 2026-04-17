<template>
  <header class="w-full bg-white border-b border-gray-200 h-20 px-6 md:px-12 flex items-center justify-between shadow-sm shrink-0 z-50">
    <div class="flex items-center gap-16 flex-1">
      <h1 class="text-3xl font-black text-[#064E3B] tracking-tighter cursor-pointer">
        SportLink
      </h1>

      <div class="hidden md:block relative w-full max-w-2xl">
        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input
          type="text"
          placeholder="Rechercher des athlètes..."
          class="w-full bg-gray-100 border-none rounded-xl py-3 pl-12 pr-4 focus:ring-2 focus:ring-[#064E3B] outline-none transition-all"
        >
      </div>
    </div>

    <div class="flex items-center gap-6 ml-8">
      <button class="relative text-gray-500 hover:text-[#064E3B] transition-colors">
        <i class="fa-solid fa-bell text-2xl"></i>
        <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full border-2 border-white"></span>
      </button>

      <div class="h-11 w-11 rounded-full border-2 border-gray-200 overflow-hidden cursor-pointer hover:border-[#064E3B] transition-colors">
        <img
          :src="`https://ui-avatars.com/api/?name=${user?.name || 'User'}&bg=064E3B&color=fff`"
          alt="User Profile"
          class="w-full h-full object-cover"
        >
      </div>
    </div>
  </header>
</template>

<script setup>
import { useAuthStore } from '@/stores/AuthStore'
import { storeToRefs } from 'pinia'

const authStore = useAuthStore()
const { user } = storeToRefs(authStore)
</script>
