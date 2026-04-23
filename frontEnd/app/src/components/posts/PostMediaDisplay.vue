<template>
  <div :class="gridClass" class="relative gap-1 overflow-hidden rounded-xl mt-3 bg-gray-100">
    <div v-for="(item, index) in displayMedia" :key="index" class="relative overflow-hidden">
      <img v-if="item.mediaType === 'IMAGE'" :src="item.url" class="w-full h-full object-cover shadow-sm">
      <video v-else :src="item.url" controls class="w-full h-full object-cover"></video>

      <div v-if="index === 3 && media.length > 4" class="absolute inset-0 bg-black/60 flex items-center justify-center">
        <span class="text-white text-xl font-bold">+{{ media.length - 4 }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps(['media'])

const displayMedia = computed(() => props.media?.slice(0, 4) || [])

const gridClass = computed(() => {
  const count = displayMedia.value.length
  if (count === 1) return 'grid-cols-1 max-h-[500px]'
  if (count === 2) return 'grid-cols-2 h-[250px]'
  if (count === 3) return 'grid grid-cols-2 h-[350px] [&>*:first-child]:row-span-2'
  return 'grid-cols-2 h-[350px]'
})
</script>
