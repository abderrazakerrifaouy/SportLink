<template>
  <div class="space-y-6">
    <div v-if="loading" class="flex justify-center py-14">
      <div class="h-10 w-10 animate-spin rounded-full border-b-2 border-indigo-600"></div>
    </div>

    <div v-else-if="error" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
      {{ error }}
    </div>

    <div v-else-if="title" class="space-y-6">
      <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm ring-1 ring-slate-900/5">
        <div class="bg-linear-to-r from-indigo-600 via-blue-600 to-cyan-500 px-6 py-8 text-white">
          <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
              <p class="text-xs font-bold uppercase tracking-[0.3em] text-white/80">Title Details</p>
              <h1 class="mt-2 text-3xl font-black tracking-tight">{{ title.nomTitre }}</h1>
              <p class="mt-3 max-w-3xl text-sm text-white/85">{{ title.description }}</p>
            </div>

            <button
              type="button"
              @click="router.push({ name: 'ClubAdminTitles' })"
              class="inline-flex items-center self-start rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/25"
            >
              <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
              Back to titles
            </button>
          </div>

          <div class="mt-6 flex flex-wrap gap-2 text-xs font-bold uppercase tracking-wider text-white/90">
            <span class="rounded-full bg-white/15 px-3 py-1">{{ title.number }} Wins</span>
            <span class="rounded-full bg-white/15 px-3 py-1">{{ title.history?.length || 0 }} Records</span>
          </div>
        </div>
      </section>

      <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="mb-5 flex items-center justify-between gap-3">
            <div>
              <h2 class="text-lg font-bold text-slate-900">Achievement History</h2>
              <p class="mt-1 text-sm text-slate-500">All recorded wins for this title.</p>
            </div>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
              {{ title.history?.length || 0 }} entries
            </span>
          </div>

          <div v-if="title.history?.length" class="space-y-3">
            <article
              v-for="record in title.history"
              :key="record.id"
              class="flex items-center gap-4 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-4"
            >
              <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-lg font-black text-white shadow-sm">
                #{{ record.win_number }}
              </div>
              <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-slate-900">{{ record.label }}</p>
                <p class="mt-1 text-xs text-slate-500">Achievement recorded from backend title counter</p>
              </div>
            </article>
          </div>

          <div v-else class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
            No history records found.
          </div>
        </div>

        <aside class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <h2 class="text-lg font-bold text-slate-900">Summary</h2>
          <dl class="mt-5 space-y-4 text-sm">
            <div class="flex items-start justify-between gap-4">
              <dt class="text-slate-500">Title</dt>
              <dd class="text-right font-semibold text-slate-900">{{ title.nomTitre }}</dd>
            </div>
            <div class="flex items-start justify-between gap-4">
              <dt class="text-slate-500">Total Wins</dt>
              <dd class="text-right font-semibold text-slate-900">{{ title.number }}</dd>
            </div>
            <div class="flex items-start justify-between gap-4">
              <dt class="text-slate-500">Records</dt>
              <dd class="text-right font-semibold text-slate-900">{{ title.history?.length || 0 }}</dd>
            </div>
          </dl>
        </aside>
      </section>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api/api'
import clubAdminService from '@/services/cloubAdminService'

const route = useRoute()
const router = useRouter()
const loading = ref(true)
const error = ref(null)
const title = ref(null)

const fetchTitle = async () => {
  loading.value = true
  error.value = null

  try {
    const titleId = route.params.id
    const response = await clubAdminService.getTitre(titleId)
    title.value = response.data?.data || response.data

    if (!title.value && titleId) {
      const fallback = await api.get(`/titres/${titleId}`)
      title.value = fallback.data?.data || fallback.data
    }
  } catch (err) {
    error.value = err?.response?.data?.message || 'Failed to load title details.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchTitle)
</script>
