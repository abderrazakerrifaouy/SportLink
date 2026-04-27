<template>
  <div class="space-y-6">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <p class="text-xs font-bold uppercase tracking-[0.35em] text-slate-400">Signalements</p>
          <h2 class="mt-2 text-2xl font-black text-slate-900">Examiner les signalements</h2>
          <p class="mt-2 text-sm leading-6 text-slate-500">Traitez ou rejetez les signalements pour garder la plateforme sure.</p>
        </div>

        <div class="flex w-full flex-col gap-3 lg:max-w-2xl lg:flex-row">
          <input
            v-model="query"
            type="search"
            placeholder="Rechercher des signalements par motif"
            class="min-w-0 flex-1 rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-cyan-400"
            @keyup.enter="loadReports"
          />
          <select v-model="status" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-cyan-400">
            <option value="all">Tous</option>
            <option value="pending">En attente</option>
            <option value="resolved">Resolus</option>
            <option value="dismissed">Rejetes</option>
          </select>
          <button type="button" class="rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800" @click="loadReports">
            Charger
          </button>
        </div>
      </div>

      <p v-if="localError" class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        {{ localError }}
      </p>
    </section>

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
      <div v-if="isLoading" class="flex justify-center py-16 text-slate-500">
        <div class="h-8 w-8 animate-spin rounded-full border-2 border-slate-300 border-t-cyan-500"></div>
      </div>

      <div v-else-if="!rows.length" class="px-6 py-16 text-center text-slate-500">
        Aucun signalement trouve.
      </div>

      <table v-else class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-[0.28em] text-slate-400">
          <tr>
            <th class="px-6 py-4">Signalement</th>
            <th class="px-6 py-4">Cible</th>
            <th class="px-6 py-4">Auteur</th>
            <th class="px-6 py-4">Statut</th>
            <th class="px-6 py-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="report in rows" :key="report.id" class="align-top hover:bg-slate-50/60">
            <td class="px-6 py-4">
              <p class="max-w-2xl text-sm font-medium text-slate-900">{{ report.reason }}</p>
              <p class="mt-1 text-xs text-slate-500">ID: {{ report.id }} · {{ formatDate(report.created_at) }}</p>
            </td>
            <td class="px-6 py-4 text-sm text-slate-600">
              <p class="font-semibold text-slate-900">{{ reportTargetLabel(report) }}</p>
              <p class="text-xs text-slate-500">{{ formatReportableType(report.reportable_type) }}</p>
            </td>
            <td class="px-6 py-4 text-sm text-slate-600">{{ report.reporter?.name || 'Inconnu' }}</td>
            <td class="px-6 py-4">
              <span :class="statusClass(report.status)" class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-[0.22em]">
                {{ report.status }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex flex-wrap justify-end gap-2">
                <button
                  v-if="report.status === 'pending'"
                  type="button"
                  class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100"
                  :disabled="workingId === String(report.id)"
                  @click="updateReport(report.id, 'resolved')"
                >
                  Resoudre
                </button>
                <button
                  v-if="report.status === 'pending'"
                  type="button"
                  class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-100"
                  :disabled="workingId === String(report.id)"
                  @click="updateReport(report.id, 'dismissed')"
                >
                  Rejeter
                </button>
                <button
                  type="button"
                  class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100"
                  :disabled="workingId === String(report.id)"
                  @click="deleteReport(report.id)"
                >
                  Supprimer
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import { useAdminStore } from '@/stores/adminStore'

const adminStore = useAdminStore()
const query = ref('')
const status = ref('all')
const localError = ref('')
const workingId = ref('')
const rows = ref([])
const isLoading = ref(false)

const formatDate = (value) => (value ? new Date(value).toLocaleDateString('fr-FR', { year: 'numeric', month: 'short', day: 'numeric' }) : '-')

const formatReportableType = (type) => {
  if (!type) return 'Inconnu'
  // Extract clean name from PHP class path like "App\Models\User" → "User"
  const parts = type.split('\\')
  return parts[parts.length - 1] || type
}

const reportTargetLabel = (report) => {
  const target = report.reportable
  if (!target) return 'Cible supprimee'
  if (target.name) return target.name
  if (target.nomClub) return target.nomClub
  if (target.content) return String(target.content).slice(0, 80)
  return `#${target.id || report.reportable_id}`
}

const statusClass = (value) => {
  if (value === 'resolved') return 'bg-emerald-100 text-emerald-700'
  if (value === 'dismissed') return 'bg-amber-100 text-amber-700'
  return 'bg-rose-100 text-rose-700'
}

const loadReports = async () => {
  localError.value = ''
  isLoading.value = true
  try {
    const statusParam = status.value === 'all' ? null : status.value
    const queryParam = query.value.trim() || null
    const response = await adminStore.fetchReports(statusParam, queryParam)
    rows.value = response.data || response
  } catch (error) {
    localError.value = error?.response?.data?.message || adminStore.error || 'Impossible de charger les signalements.'
  } finally {
    isLoading.value = false
  }
}

const updateReport = async (id, nextStatus) => {
  workingId.value = String(id)
  localError.value = ''

  try {
    await adminStore.resolveReport(id, nextStatus)
    const report = rows.value.find((item) => String(item.id) === String(id))
    if (report) report.status = nextStatus
  } catch (error) {
    localError.value = error?.response?.data?.message || adminStore.error || 'Impossible de mettre a jour ce signalement.'
  } finally {
    workingId.value = ''
  }
}

const deleteReport = async (id) => {
  if (!window.confirm('Supprimer ce signalement ?')) return

  workingId.value = String(id)
  localError.value = ''

  try {
    await adminStore.deleteReport(id)
    rows.value = rows.value.filter((report) => String(report.id) !== String(id))
  } catch (error) {
    localError.value = error?.response?.data?.message || adminStore.error || 'Impossible de supprimer ce signalement.'
  } finally {
    workingId.value = ''
  }
}

watch(status, loadReports)

onMounted(loadReports)
</script>
