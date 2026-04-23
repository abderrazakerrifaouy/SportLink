<template>
  <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200/80 bg-slate-100/80">
    <div :class="gridClass" class="grid gap-1.5 p-1.5">
      <div
        v-for="(item, index) in displayMedia"
        :key="`${item.url}-${index}`"
        class="group relative flex items-center justify-center overflow-hidden rounded-xl bg-slate-900/90"
      >
        <video
          v-if="isVideo(item)"
          :src="item.url"
          :ref="(el) => setVideoRef(el, index)"
          controls
          muted
          playsinline
          preload="metadata"
          class="max-h-full max-w-full object-contain"
        ></video>

        <img
          v-else
          :src="item.url"
          class="max-h-full max-w-full object-contain transition duration-300 group-hover:scale-[1.01]"
          alt="Post media"
          loading="lazy"
        >

        <div
          v-if="index === 3 && media.length > 4"
          class="absolute inset-0 flex items-center justify-center bg-slate-900/65"
        >
          <span class="rounded-full bg-white/20 px-3 py-1 text-sm font-bold text-white backdrop-blur-sm">
            +{{ media.length - 4 }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'

const props = defineProps({
  media: {
    type: Array,
    default: () => []
  }
})

const displayMedia = computed(() => props.media.slice(0, 4))
const videoRefs = ref([])
let observer = null

const setVideoRef = (el, index) => {
  videoRefs.value[index] = el || null
}

const stopVideo = (videoEl) => {
  if (!videoEl) return
  videoEl.pause()
  if (!videoEl.seeking) {
    videoEl.currentTime = 0
  }
}

const setupObserver = async () => {
  if (observer) {
    observer.disconnect()
  }

  await nextTick()

  observer = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        const videoEl = entry.target

        if (entry.isIntersecting && entry.intersectionRatio >= 0.6) {
          const playPromise = videoEl.play()
          if (playPromise?.catch) {
            playPromise.catch(() => {
              // Ignore autoplay rejections from browser policies.
            })
          }
        } else {
          stopVideo(videoEl)
        }
      }
    },
    {
      threshold: [0, 0.6, 1]
    }
  )

  for (const videoEl of videoRefs.value) {
    if (videoEl) {
      observer.observe(videoEl)
    }
  }
}

const gridClass = computed(() => {
  const count = displayMedia.value.length

  if (count <= 1) return 'grid-cols-1 h-[280px] sm:h-[380px] md:h-[500px]'
  if (count === 2) return 'grid-cols-2 h-[220px] sm:h-[300px] md:h-[360px]'
  if (count === 3) return 'grid-cols-2 h-[280px] sm:h-[360px] md:h-[420px] [&>*:first-child]:row-span-2'
  return 'grid-cols-2 h-[280px] sm:h-[360px] md:h-[440px]'
})

const isVideo = (item) => {
  const mediaType = item?.mediaType?.toUpperCase?.() || ''
  if (mediaType === 'VIDEO') return true

  const url = item?.url || ''
  return /\.(mp4|webm|mov|m4v)(\?.*)?$/i.test(url) || url.includes('/video/upload/')
}

onMounted(() => {
  setupObserver()
})

watch(
  () => displayMedia.value,
  async () => {
    videoRefs.value = []
    await setupObserver()
  },
  { deep: true }
)

onBeforeUnmount(() => {
  if (observer) {
    observer.disconnect()
    observer = null
  }

  for (const videoEl of videoRefs.value) {
    stopVideo(videoEl)
  }
})
</script>
