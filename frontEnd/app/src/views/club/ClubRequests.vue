<script setup>
import { computed, ref, watch } from 'vue'
import { useAuthStore } from '@/stores/AuthStore'
import clubRequestService from '@/services/clubRequestService'

const authStore = useAuthStore()
const loading = ref(true)
const error = ref(null)
const requests = ref([])

const fetchRequests = async () => {
  if (!authStore.user?.id) return

  loading.value = true
  error.value = null

  try {
    const response = await clubRequestService.getHistory('club-admin')
    requests.value = response.data?.data || response.data || []
  } catch (err) {
    error.value = err?.response?.data?.message || 'Failed to load request history.'
    requests.value = []
  } finally {
    loading.value = false
  }
}

const statusCounts = computed(() => {
  return requests.value.reduce((counts, request) => {
    const key = (request.status || 'PENDING').toLowerCase()
    counts[key] = (counts[key] || 0) + 1
    return counts
  }, { pending: 0, accepted: 0, rejected: 0 })
})

const statusClass = (status) => {
  const map = {
    PENDING: 'bg-amber-50 text-amber-700 border-amber-100',
    ACCEPTED: 'bg-emerald-50 text-emerald-700 border-emerald-100',
    REJECTED: 'bg-rose-50 text-rose-700 border-rose-100',
  }

  return map[status] || map.PENDING
}

const formatDate = (value) => {
  if (!value) return 'Just now'
  return new Date(value).toLocaleDateString('en-GB', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

watch(
  () => authStore.user?.id,
  () => {
    fetchRequests()
  },
  { immediate: true }
)
</script>

<template>
  <div class="space-y-6">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <p class="text-xs font-bold uppercase tracking-[0.32em] text-indigo-600">Club Requests</p>
          <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900">Request History</h1>
          <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
            Track every invitation your club has sent and its final status in one compact history view.
          </p>
        </div>

        <div class="grid grid-cols-3 gap-3 text-center text-xs font-semibold text-slate-500">
          <div class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
            <p class="text-lg font-black text-slate-900">{{ statusCounts.pending }}</p>
            <p class="mt-1 uppercase tracking-wider">Pending</p>
          </div>
          <div class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
            <p class="text-lg font-black text-slate-900">{{ statusCounts.accepted }}</p>
            <p class="mt-1 uppercase tracking-wider">Accepted</p>
          </div>
          <div class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
            <p class="text-lg font-black text-slate-900">{{ statusCounts.rejected }}</p>
            <p class="mt-1 uppercase tracking-wider">Rejected</p>
          </div>
        </div>
      </div>
    </section>

    <div v-if="loading" class="flex justify-center py-14">
      <div class="h-10 w-10 animate-spin rounded-full border-b-2 border-indigo-600"></div>
    </div>

    <div v-else-if="error" class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-700">
      {{ error }}
    </div>

    <div v-else-if="requests.length === 0" class="rounded-3xl border border-dashed border-slate-200 bg-white px-6 py-14 text-center text-slate-500">
      <svg class="mx-auto mb-4 h-14 w-14 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
      </svg>
      <p class="font-semibold text-slate-700">No club request history yet.</p>
      <p class="mt-1 text-sm text-slate-500">When you invite players, every request will appear here.</p>
    </div>

    <div v-else class="grid grid-cols-1 gap-5 xl:grid-cols-2">
      <article
        v-for="request in requests"
        :key="request.id"
        class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
      >
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0">
            <p class="text-xs font-bold uppercase tracking-[0.28em] text-slate-400">{{ formatDate(request.created_at) }}</p>
            <h2 class="mt-2 truncate text-xl font-black tracking-tight text-slate-900">
              {{ request.joueur?.user?.name || 'Unknown player' }}
            </h2>
            <p class="mt-1 line-clamp-2 text-sm leading-6 text-slate-500">
              {{ request.joueur?.user?.email || 'No email available' }}
            </p>
          </div>

          <span class="shrink-0 rounded-full border px-3 py-1 text-xs font-bold uppercase tracking-wider" :class="statusClass(request.status)">
            {{ request.status }}
          </span>
        </div>

        <div class="mt-5 grid gap-3 rounded-2xl bg-slate-50 p-4 text-sm text-slate-600 sm:grid-cols-2">
          <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Club name</p>
            <p class="mt-1 font-semibold text-slate-900">{{ request.club?.nomClub || 'Unknown club' }}</p>
          </div>
          <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Player club status</p>
            <p class="mt-1 font-semibold text-slate-900">{{ request.experience ? 'Experience linked' : 'No experience yet' }}</p>
          </div>
        </div>

        <div v-if="request.experience" class="mt-4 rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4 text-sm text-emerald-800">
          Experience created on {{ formatDate(request.experience.joinDate) }}.
        </div>
      </article>
    </div>
  </div>
</template>
