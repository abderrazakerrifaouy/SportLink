<template>
  <Teleport to="body">
    <Transition name="modal-fade">
      <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
          <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-900">Report Content</h2>
            <button
              type="button"
              class="text-slate-400 transition hover:text-slate-600"
              @click="close"
            >
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <form @submit.prevent="submitReport" class="space-y-4">
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">
                Reason for Report
              </label>
              <select
                v-model="selectedReason"
                class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400"
                required
              >
                <option value="">Select a reason...</option>
                <option value="Inappropriate content">Inappropriate content</option>
                <option value="Spam">Spam</option>
                <option value="Harassment or bullying">Harassment or bullying</option>
                <option value="Hate speech">Hate speech</option>
                <option value="Violence or dangerous behavior">Violence or dangerous behavior</option>
                <option value="Misinformation">Misinformation</option>
                <option value="Intellectual property violation">Intellectual property violation</option>
                <option value="Other">Other</option>
              </select>
            </div>

            <!-- Details -->
            <div>
              <label for="details" class="block text-sm font-semibold text-slate-700 mb-2">
                Additional Details (optional)
              </label>
              <textarea
                id="details"
                v-model="details"
                rows="4"
                maxlength="1000"
                placeholder="Provide more context about why you're reporting this content..."
                class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-slate-900 outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400"
              ></textarea>
              <p class="mt-1 text-xs text-slate-500">
                {{ details.length }}/1000 characters
              </p>
            </div>

            <div v-if="error" class="rounded-lg bg-red-50 p-3 text-sm text-red-700">
              {{ error }}
            </div>

            <div class="flex gap-3 pt-2">
              <button
                type="button"
                @click="close"
                class="flex-1 rounded-lg border border-slate-200 bg-white px-4 py-2.5 font-semibold text-slate-700 transition hover:bg-slate-50"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isSubmitting || !selectedReason"
                class="flex-1 rounded-lg bg-indigo-600 px-4 py-2.5 font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
              >
                {{ isSubmitting ? 'Submitting...' : 'Submit Report' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue'
import { useReportStore } from '@/stores/reportStore'

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  },
  reportableType: {
    type: String,
    required: true
  },
  reportableId: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['close', 'success'])

const reportStore = useReportStore()
const selectedReason = ref('')
const details = ref('')
const isSubmitting = ref(false)
const error = ref('')

const close = () => {
  selectedReason.value = ''
  details.value = ''
  error.value = ''
  emit('close')
}

const submitReport = async () => {
  if (!selectedReason.value) {
    error.value = 'Please select a reason for reporting'
    return
  }

  isSubmitting.value = true
  error.value = ''

  try {
    const reason = details.value
      ? `${selectedReason.value}: ${details.value}`
      : selectedReason.value

    await reportStore.createReport(props.reportableType, props.reportableId, reason)

    emit('success')
    close()
  } catch (err) {
    error.value = err || 'Failed to submit report. Please try again.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
