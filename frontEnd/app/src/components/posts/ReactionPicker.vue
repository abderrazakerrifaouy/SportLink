<template>
  <div class="relative flex items-center gap-1">
    <div class="flex items-center gap-1">
      <button
        v-for="type in activeReactions"
        :key="type"
        @click="handleSelect(type)"
        class="flex items-center gap-1.5 px-2.5 py-1 rounded-full transition-all duration-200 border"
        :class="hasReaction(type)
          ? 'bg-blue-50 border-blue-200 text-blue-600 shadow-sm'
          : 'bg-gray-50 border-transparent hover:bg-gray-100 text-gray-600'"
      >
        <span class="text-base leading-none">{{ getEmoji(type) }}</span>
        <span v-if="getCount(type)" class="text-xs font-bold">
          {{ getCount(type) }}
        </span>
      </button>
    </div>

    <button
      @click="togglePicker"
      class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-colors"
      :class="{ 'text-blue-600 bg-blue-50': showPicker }"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
      </svg>
    </button>

    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform scale-95 opacity-0 -translate-y-2"
      enter-to-class="transform scale-100 opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="transform scale-100 opacity-100 translate-y-0"
      leave-to-class="transform scale-95 opacity-0 -translate-y-2"
    >
      <div
        v-if="showPicker"
        class="absolute bottom-full mb-2 left-0 p-1.5 bg-white rounded-full shadow-xl border border-gray-100 flex gap-1 z-30"
      >
        <button
          v-for="type in REACTION_TYPES"
          :key="type"
          @click="handleSelect(type)"
          class="p-2 hover:bg-blue-50 rounded-full transition-transform hover:scale-125 duration-200 text-xl"
          :title="type"
        >
          {{ getEmoji(type) }}
        </button>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePostStore } from '@/stores/PostStore'

const props = defineProps({
  postId: {
    type: [Number, String],
    required: true
  },
  reactionsSummary: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['react'])
const postStore = usePostStore()
const showPicker = ref(false)

const EMOJI_MAP = {
  LIKE: '👍',
  LOVE: '❤️',
  HAHA: '😂',
  WOW: '😮',
  SAD: '😢',
  GRR: '😠',
  DISLIKE: '👎',
}

const REACTION_TYPES = Object.keys(EMOJI_MAP)

const activeReactions = computed(() => {
  return REACTION_TYPES.filter(type => {
    return getCount(type) > 0 || hasReaction(type)
  })
})

const getEmoji = (type) => EMOJI_MAP[type] || '👍'

const getCount = (type) => {
  const key = `${type.toLowerCase()}s_count`
  return props.reactionsSummary[key] || 0
}

const hasReaction = (type) => {
  return postStore.getUserReactionType(props.postId) === type
}

const togglePicker = () => {
  showPicker.value = !showPicker.value
}

const handleSelect = (type) => {
  emit('react', { postId: props.postId, type })
  showPicker.value = false
}
</script>
