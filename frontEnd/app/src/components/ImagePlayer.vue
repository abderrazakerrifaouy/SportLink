<script setup>
import { ref } from 'vue'

const props = defineProps({
  src: {
    type: String,
    required: true
  },
  alt: {
    type: String,
    default: 'Post image'
  },
  aspectRatio: {
    type: String,
    default: 'aspect-video' // t9der t-passi 'aspect-square' aw 'aspect-auto'
  }
})

const isLoaded = ref(false)
const hasError = ref(false)

const handleLoad = () => {
  isLoaded.value = true
}

const handleError = () => {
  hasError.value = true
  isLoaded.value = true // Bach n-hidiw l-skeleton
}
</script>

<template>
  <div :class="['relative overflow-hidden bg-gray-100 rounded-2xl w-full', aspectRatio]">

    <div
      v-if="!isLoaded"
      class="absolute inset-0 animate-pulse bg-gray-200 flex items-center justify-center"
    >
      <i class="fa-regular fa-image text-gray-400 text-3xl"></i>
    </div>

    <div
      v-if="hasError"
      class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 text-gray-400"
    >
      <i class="fa-solid fa-circle-exclamation text-2xl mb-2"></i>
      <span class="text-[10px] font-bold uppercase tracking-wider">Image non disponible</span>
    </div>

    <img
      v-if="!hasError"
      :src="src"
      :alt="alt"
      loading="lazy"
      @load="handleLoad"
      @error="handleError"
      class="w-full h-full object-cover transition-all duration-700 ease-in-out"
      :class="[isLoaded ? 'opacity-100 scale-100' : 'opacity-0 scale-105']"
    />

    <div class="absolute inset-0 ring-1 ring-inset ring-black/5 rounded-2xl pointer-events-none"></div>
  </div>
</template>

<style scoped>
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: .5; }
}
</style>
