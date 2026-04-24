<!-- Example: How to Add Report Button to PostCard.vue -->

<template>
  <article class="mb-6">
    <!-- Existing post header with three-dot menu -->
    <header class="flex items-center justify-between border-b px-5 py-4">
      <!-- ... existing user info ... -->
      
      <div v-if="canManagePost || canReportPost" class="relative">
        <button
          type="button"
          class="rounded-full p-2 text-slate-500 transition hover:bg-slate-100"
          @click.stop="showPostMenu = !showPostMenu"
        >
          <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </button>

        <div v-if="showPostMenu" class="absolute right-0 z-20 mt-2 w-36 rounded-2xl border border-slate-200 bg-white shadow-xl">
          <!-- Owner-only options -->
          <button
            v-if="canManagePost"
            type="button"
            class="block w-full px-4 py-2.5 text-left text-sm font-semibold text-slate-700 hover:bg-slate-50"
            @click="startEditPost"
          >
            Edit
          </button>
          <button
            v-if="canManagePost"
            type="button"
            class="block w-full px-4 py-2.5 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50"
            @click="confirmDeletePost"
          >
            Delete
          </button>
          
          <!-- Report option for other users -->
          <button
            v-if="canReportPost"
            type="button"
            class="block w-full px-4 py-2.5 text-left text-sm font-semibold text-slate-700 hover:bg-slate-50 border-t border-slate-200"
            @click="openReportModal"
          >
            Report
          </button>
        </div>
      </div>
    </header>

    <!-- ... existing post content ... -->

    <!-- Report Modal Component -->
    <ReportModal
      :is-open="showReportModal"
      reportable-type="App\\Models\\Post"
      :reportable-id="post.id"
      @close="showReportModal = false"
      @success="handleReportSuccess"
    />
  </article>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/AuthStore'
import ReportModal from '@/components/ReportModal.vue'

const props = defineProps({
  post: {
    type: Object,
    required: true
  }
})

const authStore = useAuthStore()
const showReportModal = ref(false)

const canManagePost = computed(() => {
  return authStore.user?.id === props.post.author?.id
})

const canReportPost = computed(() => {
  return authStore.user?.id && authStore.user.id !== props.post.author?.id
})

const openReportModal = () => {
  showReportModal.value = true
  showPostMenu.value = false
}

const handleReportSuccess = () => {
  // Show success notification
  console.log('Post reported successfully')
  // You can emit an event or show a toast notification here
}
</script>
