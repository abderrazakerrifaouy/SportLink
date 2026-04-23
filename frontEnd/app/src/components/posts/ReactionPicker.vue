<template>
  <div class="relative inline-block">
    <div
      class="relative group"
      @mouseenter="showPicker = true"
      @mouseleave="handleMouseLeave"
    >
      <button
        type="button"
        @click.stop="$emit('toggle')"
        class="flex items-center gap-2 px-4 py-2 hover:bg-gray-100 rounded-lg transition-all duration-200 font-semibold text-[14px]"
        :style="{ color: userReactionColor }"
      >
        <span class="text-xl transition-transform duration-200" :class="{ 'scale-125': userReaction }">
          {{ userReactionEmoji || '👍' }}
        </span>
        <span class="capitalize">{{ userReactionLabel || 'Like' }}</span>
      </button>

      <Transition name="reaction-pop">
        <div
          v-if="showPicker"
          class="absolute bottom-full left-0 mb-2 p-1.5 bg-white rounded-full shadow-xl border border-gray-100 flex gap-1 z-100 animate-in fade-in slide-in-from-bottom-2"
          @mouseenter="clearTimer"
        >
          <button
            v-for="(emoji, type) in emojiMap"
            :key="type"
            type="button"
            @click.stop="selectReaction(type)"
            class="text-3xl hover:scale-150 transition-transform duration-200 origin-bottom px-1.5 py-1"
            :title="type"
          >
            {{ emoji }}
          </button>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  userReaction: {
    type: String,
    default: null
  }
});

const emit = defineEmits(['select', 'toggle']);

const showPicker = ref(false);
let timer = null;

const emojiMap = {
  LIKE: '👍',
  LOVE: '❤️',
  HAHA: '😂',
  WOW: '😮',
  SAD: '😢',
  GRR: '😠'
};

const userReactionEmoji = computed(() => emojiMap[props.userReaction]);

const userReactionLabel = computed(() => {
  if (!props.userReaction) return null;
  return props.userReaction.toLowerCase();
});

const userReactionColor = computed(() => {
  if (!props.userReaction) return '#65676b';
  const colors = {
    LIKE: '#1877f2',
    LOVE: '#f02849',
    HAHA: '#f7b928',
    WOW: '#f7b928',
    SAD: '#f7b928',
    GRR: '#e07a5f'
  };
  return colors[props.userReaction] || '#1877f2';
});

const selectReaction = (type) => {
  emit('select', type);
  showPicker.value = false;
};

const handleMouseLeave = () => {
  // Delay bach l-picker may-t-sedch dghya ila l-mouse khrjat b-ghlat
  timer = setTimeout(() => {
    showPicker.value = false;
  }, 500);
};

const clearTimer = () => {
  if (timer) clearTimeout(timer);
};
</script>

<style scoped>
/* Animation bhal l-Facebook style */
.reaction-pop-enter-active {
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.reaction-pop-leave-active {
  transition: all 0.2s ease-in;
}
.reaction-pop-enter-from {
  opacity: 0;
  transform: translateY(10px) scale(0.5);
}
.reaction-pop-leave-to {
  opacity: 0;
  transform: translateY(10px) scale(0.9);
}
</style>
