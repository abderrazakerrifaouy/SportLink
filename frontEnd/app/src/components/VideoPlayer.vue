<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  src: {
    type: String,
    required: true
  },
  poster: {
    type: String,
    default: ''
  }
});

const videoRef = ref(null);
const isPlaying = ref(false);
const isMuted = ref(true); // Default muted bhal social media
const isLoaded = ref(false);
const hasError = ref(false);

// Logic dial l-play/pause
const togglePlay = () => {
  if (!videoRef.value) return;
  if (videoRef.value.paused) {
    videoRef.value.play().catch(() => {});
    isPlaying.value = true;
  } else {
    videoRef.value.pause();
    isPlaying.value = false;
  }
};

const toggleMute = (e) => {
  e.stopPropagation(); // Bach may-dirch play/pause fach t-cliqui 3la mute
  isMuted.value = !isMuted.value;
  if (videoRef.value) videoRef.value.muted = isMuted.value;
};

const onMetadataLoaded = () => {
  isLoaded.value = true;
};

const onError = () => {
  hasError.value = true;
  isLoaded.value = true;
};

// Auto-play fach kiban f l-ecran (Intersection Observer)
let observer = null;
onMounted(() => {
  observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        videoRef.value?.play().catch(() => {});
        isPlaying.value = true;
      } else {
        videoRef.value?.pause();
        isPlaying.value = false;
      }
    });
  }, { threshold: 0.6 }); // Khass 60% dial video t-ban bach y-khedem

  if (videoRef.value) observer.observe(videoRef.value);
});

onUnmounted(() => {
  if (observer) observer.disconnect();
});
</script>

<template>
  <div class="relative w-full h-full bg-black rounded-2xl overflow-hidden group shadow-inner">

    <div v-if="!isLoaded" class="absolute inset-0 flex items-center justify-center z-10 bg-neutral-900">
      <div class="w-8 h-8 border-4 border-white/10 border-t-emerald-500 rounded-full animate-spin"></div>
    </div>

    <div v-if="hasError" class="absolute inset-0 flex flex-col items-center justify-center bg-neutral-800 text-white p-4 text-center z-20">
      <i class="fa-solid fa-video-slash text-3xl mb-2 text-red-500/50"></i>
      <p class="text-[10px] font-bold uppercase tracking-widest opacity-60">Vidéo indisponible</p>
    </div>

    <video
      ref="videoRef"
      :src="src"
      :poster="poster"
      class="w-full h-full object-cover cursor-pointer transition-all duration-700"
      :class="{ 'opacity-100 scale-100': isLoaded, 'opacity-0 scale-105': !isLoaded }"
      playsinline
      :muted="isMuted"
      loop
      preload="metadata"
      @loadedmetadata="onMetadataLoaded"
      @error="onError"
      @click="togglePlay"
    ></video>

    <div
      v-if="isLoaded && !hasError"
      @click="togglePlay"
      class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer z-10"
    >
      <div class="w-14 h-14 flex items-center justify-center rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white shadow-xl">
        <i :class="['fa-solid text-xl', isPlaying ? 'fa-pause' : 'fa-play ml-1']"></i>
      </div>
    </div>

    <button
      v-if="isLoaded && !hasError"
      @click="toggleMute"
      class="absolute bottom-3 left-3 w-8 h-8 flex items-center justify-center rounded-lg bg-black/40 backdrop-blur-md border border-white/10 text-white z-20 hover:bg-black/60 transition"
    >
      <i :class="['fa-solid text-[12px]', isMuted ? 'fa-volume-xmark' : 'fa-volume-high']"></i>
    </button>

    <div v-if="isLoaded" class="absolute bottom-3 right-3 bg-emerald-600/80 backdrop-blur-md text-white text-[9px] px-2 py-1 rounded-md font-black uppercase tracking-tighter z-20">
      SportLink Play
    </div>
  </div>
</template>

<style scoped>
/* Hidden default controls for a cleaner look */
video::-webkit-media-controls {
  display: none !important;
}
</style>
