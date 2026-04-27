<template>
  <div class="space-y-6">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <p class="text-xs font-bold uppercase tracking-[0.35em] text-slate-400">Publications</p>
          <h2 class="mt-2 text-2xl font-black text-slate-900">Rechercher et supprimer des publications</h2>
          <p class="mt-2 text-sm leading-6 text-slate-500">Passez en revue le contenu et supprimez rapidement les publications inappropriees.</p>
        </div>

        <form class="flex w-full gap-3 lg:max-w-xl" @submit.prevent="submitSearch">
          <input
            v-model="query"
            type="search"
            placeholder="Rechercher des publications par contenu"
            class="min-w-0 flex-1 rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-cyan-400"
          />
          <button type="submit" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
            Rechercher
          </button>
          <button type="button" class="rounded-2xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50" @click="resetSearch">
            Reinitialiser
          </button>
        </form>
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
        Aucune publication trouvee.
      </div>

      <table v-else class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-[0.28em] text-slate-400">
          <tr>
            <th class="px-6 py-4">Publication</th>
            <th class="px-6 py-4">Auteur</th>
            <th class="px-6 py-4">Creation</th>
            <th class="px-6 py-4 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="post in rows" :key="post.id" class="hover:bg-slate-50/60">
            <td class="px-6 py-4">
              <p class="max-w-2xl text-sm font-medium text-slate-900 line-clamp-2">{{ post.content }}</p>
              <p class="mt-1 text-xs text-slate-500">ID: {{ post.id }}</p>
            </td>
            <td class="px-6 py-4 text-sm text-slate-600">{{ post.user?.name || 'Inconnu' }}</td>
            <td class="px-6 py-4 text-sm text-slate-600">{{ formatDate(post.created_at) }}</td>
            <td class="px-6 py-4 text-right">
              <button
                type="button"
                class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100"
                :disabled="workingId === String(post.id)"
                @click="removePost(post.id)"
              >
                {{ workingId === String(post.id) ? 'Suppression...' : 'Supprimer' }}
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
const query = ref('')
const localError = ref('')
const workingId = ref('')
const rows = ref([])

const formatDate = (value) => (value ? new Date(value).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric' }) : '-')

const loadPosts = async () => {
  localError.value = ''
  try {
    const response = query.value.trim()
      ? await adminStore.searchPosts(query.value.trim())
      : await adminStore.fetchPosts()

    rows.value = response.data || response
  } catch (error) {
    localError.value = error?.response?.data?.message || adminStore.error || 'Impossible de charger les publications.'
  }
}

const submitSearch = async () => {
  await loadPosts()
}

const resetSearch = async () => {
  query.value = ''
  await loadPosts()
}

const removePost = async (id) => {
  if (!window.confirm('Supprimer cette publication ?')) return

  workingId.value = String(id)
  localError.value = ''

  try {
    await adminStore.deletePost(id)
    rows.value = rows.value.filter((post) => String(post.id) !== String(id))
  } catch (error) {
    localError.value = error?.response?.data?.message || adminStore.error || 'Impossible de supprimer cette publication.'
  } finally {
    workingId.value = ''
  }
}

onMounted(loadPosts)
</script>
