<template>
  <div class="space-y-6">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-3">
        <p class="text-xs font-bold uppercase tracking-[0.35em] text-slate-400">Commentaires</p>
        <h2 class="text-2xl font-black text-slate-900">Moderer les commentaires</h2>
        <p class="text-sm leading-6 text-slate-500">Supprimez les commentaires abusifs ou hors sujet du fil de la communaute.</p>
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
        Aucun commentaire trouve.
      </div>

      <table v-else class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-[0.28em] text-slate-400">
          <tr>
            <th class="px-6 py-4">Commentaire</th>
            <th class="px-6 py-4">Auteur</th>
            <th class="px-6 py-4">Publication</th>
            <th class="px-6 py-4">Creation</th>
            <th class="px-6 py-4 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="comment in rows" :key="comment.id" class="align-top hover:bg-slate-50/60">
            <td class="px-6 py-4">
              <p class="max-w-2xl text-sm font-medium text-slate-900 line-clamp-2">{{ comment.content }}</p>
              <p class="mt-1 text-xs text-slate-500">ID: {{ comment.id }}</p>
            </td>
            <td class="px-6 py-4 text-sm text-slate-600">{{ comment.user?.name || 'Inconnu' }}</td>
            <td class="px-6 py-4 text-sm text-slate-600">{{ comment.post?.content || 'Publication inconnue' }}</td>
            <td class="px-6 py-4 text-sm text-slate-600">{{ formatDate(comment.created_at) }}</td>
            <td class="px-6 py-4 text-right">
              <button
                type="button"
                class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100"
                :disabled="workingId === String(comment.id)"
                @click="removeComment(comment.id)"
              >
                {{ workingId === String(comment.id) ? 'Suppression...' : 'Supprimer' }}
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

const formatDate = (value) => (value ? new Date(value).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric' }) : '-')

const loadComments = async () => {
  localError.value = ''
  try {
    const response = await adminStore.fetchComments()
    rows.value = response.data || response
  } catch (error) {
    localError.value = error?.response?.data?.message || adminStore.error || 'Impossible de charger les commentaires.'
  }
}

const removeComment = async (id) => {
  if (!window.confirm('Supprimer ce commentaire ?')) return

  workingId.value = String(id)
  localError.value = ''

  try {
    await adminStore.deleteComment(id)
    rows.value = rows.value.filter((comment) => String(comment.id) !== String(id))
  } catch (error) {
    localError.value = error?.response?.data?.message || adminStore.error || 'Impossible de supprimer ce commentaire.'
  } finally {
    workingId.value = ''
  }
}

onMounted(loadComments)
</script>
