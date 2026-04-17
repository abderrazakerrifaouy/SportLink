<script setup>
import { ref, computed } from 'vue'
import ImagePlayer from './ImagePlayer.vue' // Ila knti dayrhom f files bouhdhom
import VideoPlayer from './VideoPlayer.vue'

const props = defineProps({
  post: {
    type: Object,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const totalMedia = computed(() => props.post.media?.length || 0)

// Grid Logic bach t-wrapper les images/videos t9ad
const gridClass = computed(() => {
  const count = totalMedia.value
  if (count === 1) return 'grid-cols-1'
  if (count === 2) return 'grid-cols-2 h-72'
  return 'grid-cols-2 grid-rows-2 h-96'
})
</script>

<template>
  <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden mb-6 transition-all hover:shadow-md max-w-xl mx-auto">

    <div v-if="loading" class="animate-pulse p-5">
      <div class="flex items-start gap-3 mb-4">
        <div class="h-12 w-12 bg-gray-200 rounded-2xl shrink-0"></div>
        <div class="flex-1 space-y-2 py-1">
          <div class="h-4 bg-gray-200 rounded w-1/3"></div>
          <div class="h-3 bg-gray-100 rounded w-1/4"></div>
        </div>
      </div>
      <div class="aspect-video bg-gray-100 rounded-2xl"></div>
    </div>

    <div v-else>
      <div class="p-5 flex items-start justify-between">
        <div class="flex items-center gap-3 cursor-pointer">
          <img
            :src="post.author.profileImage || `https://ui-avatars.com/api/?name=${post.author.name}`"
            class="h-12 w-12 rounded-2xl object-cover bg-gray-50 border border-gray-100 shadow-sm"
            alt="Profile"
          />
          <div>
            <h3 class="font-bold text-gray-900 leading-tight hover:text-emerald-700 transition">
              {{ post.author.name }}
            </h3>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-1">
              <span class="text-emerald-600">{{ post.author.role }}</span>
              <span class="text-gray-300">•</span>
              {{ post.created_at }}
            </p>
          </div>
        </div>
        <button class="text-gray-400 hover:bg-gray-50 p-2 rounded-xl transition">
          <i class="fa-solid fa-ellipsis-vertical"></i>
        </button>
      </div>

      <div class="px-5 pb-4">
        <p class="text-gray-800 text-sm leading-relaxed">{{ post.content }}</p>
      </div>

      <div v-if="totalMedia > 0" class="px-5">
        <div :class="['grid gap-1.5 rounded-2xl overflow-hidden bg-gray-50', gridClass]">

          <div
            v-for="(item, index) in post.media.slice(0, 3)"
            :key="index"
            :class="[
              'relative group',
              totalMedia === 3 && index === 0 ? 'row-span-2 h-full' : 'h-full'
            ]"
          >
            <ImagePlayer
              v-if="item.type === 'IMAGE'"
              :src="item.url"
              class="h-full w-full"
            />

            <VideoPlayer
              v-else-if="item.type === 'VIDEO'"
              :src="item.url"
              class="h-full w-full"
            />

            <div
              v-if="index === 2 && totalMedia > 3"
              class="absolute inset-0 bg-black/60 backdrop-blur-[2px] flex items-center justify-center transition group-hover:bg-black/70"
            >
              <span class="text-white font-black text-2xl">+{{ totalMedia - 3 }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="px-5 py-4 mt-2 flex items-center justify-between border-t border-gray-50">
        <div class="flex items-center gap-1">
          <button class="flex items-center gap-2 text-gray-500 hover:bg-emerald-50 hover:text-emerald-600 px-4 py-2 rounded-2xl transition-all font-bold text-xs group">
            <i class="fa-regular fa-heart text-lg group-hover:scale-110 transition"></i>
            {{ post.reactions_summary?.likes || 0 }}
          </button>

          <button class="flex items-center gap-2 text-gray-500 hover:bg-emerald-50 hover:text-emerald-600 px-4 py-2 rounded-2xl transition-all font-bold text-xs group">
            <i class="fa-regular fa-comment text-lg group-hover:scale-110 transition"></i>
            {{ post.comments_count || 0 }}
          </button>
        </div>

        <button class="flex items-center gap-2 text-emerald-700 bg-emerald-50/50 hover:bg-emerald-100 px-4 py-2 rounded-2xl transition-all font-black text-[10px] uppercase tracking-tighter">
          <i class="fa-solid fa-chart-simple"></i>
          Stats
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Smooth smooth transition */
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: .5; }
}
</style>
