<template>
  <div class="space-y-6">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-3">
        <p class="text-xs font-bold uppercase tracking-[0.35em] text-slate-400">Comments</p>
        <h2 class="text-2xl font-black text-slate-900">Moderate comments</h2>
        <p class="text-sm leading-6 text-slate-500">Delete abusive or irrelevant comments from the community feed.</p>
      </div>

      <p v-if="localError" class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        {{ localError }}
      </p>
    </section>

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
      <div v-if="adminStore.isLoading" class="flex justify-center py-16 text-slate-500">
        <div class="h-8 w-8 animate-spin rounded-full border-2 border-slate-300 border-t-cyan-500"></div>
      </div>

      <div v-else-if="!rows.length" class="px-6 py-16 text-center text-slate-500">
        No comments found.
      </div>

      <table v-else class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-[0.28em] text-slate-400">
          <tr>
            <th class="px-6 py-4">Comment</th>
            <th class="px-6 py-4">Author</th>
            <th class="px-6 py-4">Post</th>
            <th class="px-6 py-4">Created</th>
            <th class="px-6 py-4 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="comment in rows" :key="comment.id" class="align-top hover:bg-slate-50/60">
            <td class="px-6 py-4">
              <p class="max-w-2xl text-sm font-medium text-slate-900 line-clamp-2">{{ comment.content }}</p>
              <p class="mt-1 text-xs text-slate-500">ID: {{ comment.id }}</p>
            </td>
            <td class="px-6 py-4 text-sm text-slate-600">{{ comment.user?.name || 'Unknown' }}</td>
            <td class="px-6 py-4 text-sm text-slate-600">{{ comment.post?.content || 'Unknown post' }}</td>
            <td class="px-6 py-4 text-sm text-slate-600">{{ formatDate(comment.created_at) }}</td>
            <td class="px-6 py-4 text-right">
              <button
                type="button"
                class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100"
                :disabled="workingId === String(comment.id)"
                @click="removeComment(comment.id)"
              >
                {{ workingId === String(comment.id) ? 'Deleting...' : 'Delete' }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useAdminStore } from '@/stores/adminStore'

const adminStore = useAdminStore()
const localError = ref('')
const workingId = ref('')
const rows = ref([])

const formatDate = (value) => (value ? new Date(value).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '-')

const loadComments = async () => {
  localError.value = ''
  try {
    const response = await adminStore.fetchComments()
    rows.value = response.data || response
  } catch (error) {
    localError.value = error?.response?.data?.message || adminStore.error || 'Failed to load comments.'
  }
}

const removeComment = async (id) => {
  if (!window.confirm('Delete this comment?')) return

  workingId.value = String(id)
  localError.value = ''

  try {
    await adminStore.deleteComment(id)
    rows.value = rows.value.filter((comment) => String(comment.id) !== String(id))
  } catch (error) {
    localError.value = error?.response?.data?.message || adminStore.error || 'Failed to delete comment.'
  } finally {
    workingId.value = ''
  }
}

onMounted(loadComments)
</script>
