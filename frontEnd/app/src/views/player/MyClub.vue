<script setup>
import { computed, onMounted, ref } from 'vue'
import { usePlayerClubStore } from '@/stores/playerClubStore'

const playerClubStore = usePlayerClubStore()
const feedback = ref(null)

const club = computed(() => playerClubStore.currentClubDetails)
const clubRequest = computed(() => playerClubStore.currentClubRequest)

const formatDate = (value) => {
  if (!value) return 'N/A'

  return new Date(value).toLocaleDateString('en-GB', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const fetchData = async () => {
  feedback.value = null

  try {
    await playerClubStore.fetchMyClub()
  } catch {
    // Errors are surfaced through store state.
  }
}

const leaveClub = async () => {
  if (!club.value || playerClubStore.leaving) return

  const confirmed = window.confirm('Are you sure you want to leave your current club?')
  if (!confirmed) return

  feedback.value = null

  try {
    await playerClubStore.leaveClub()
    feedback.value = {
      type: 'success',
      message: 'You left your club successfully.',
    }
  } catch {
    feedback.value = {
      type: 'error',
      message: playerClubStore.error || 'Unable to leave your club right now.',
    }
  }
}

onMounted(fetchData)
</script>

<template>
  <div class="space-y-6">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-2">
        <p class="text-xs font-bold uppercase tracking-[0.32em] text-indigo-600">Player Club</p>
        <h1 class="text-3xl font-black tracking-tight text-slate-900">My Club</h1>
        <p class="max-w-2xl text-sm leading-6 text-slate-500">
          Review your active club membership and leave the club when needed.
        </p>
      </div>
    </section>

    <div v-if="playerClubStore.loading" class="flex justify-center py-14">
      <div class="h-10 w-10 animate-spin rounded-full border-b-2 border-indigo-600"></div>
    </div>

    <div
      v-else-if="playerClubStore.error"
      class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-700"
    >
      {{ playerClubStore.error }}
    </div>

    <div
      v-if="feedback"
      class="rounded-2xl border p-4"
      :class="feedback.type === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-700'"
    >
      {{ feedback.message }}
    </div>

    <section
      v-if="club"
      class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
    >
      <div class="bg-linear-to-r from-indigo-600 via-blue-600 to-cyan-500 px-6 py-8 text-white">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
          <div>
            <p class="text-xs font-bold uppercase tracking-[0.28em] text-white/85">Current Club</p>
            <h2 class="mt-2 text-3xl font-black tracking-tight">{{ club.nomClub || 'Unknown Club' }}</h2>
            <p class="mt-2 max-w-2xl text-sm text-white/90">{{ club.description || 'No club description provided.' }}</p>
          </div>

          <button
            type="button"
            :disabled="playerClubStore.leaving"
            @click="leaveClub"
            class="inline-flex items-center self-start rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/25 disabled:cursor-not-allowed disabled:opacity-60"
          >
            {{ playerClubStore.leaving ? 'Leaving...' : 'Quit Club' }}
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Admin</p>
          <p class="mt-2 text-sm font-semibold text-slate-900">{{ club.user?.name || 'Unknown' }}</p>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Admin Email</p>
          <p class="mt-2 text-sm font-semibold text-slate-900">{{ club.user?.email || 'Not available' }}</p>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Players</p>
          <p class="mt-2 text-sm font-semibold text-slate-900">{{ club.joueurs?.length || 0 }}</p>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
          <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Titles</p>
          <p class="mt-2 text-sm font-semibold text-slate-900">{{ club.titres?.length || 0 }}</p>
        </div>
      </div>

      <div class="border-t border-slate-100 p-6">
        <h3 class="text-sm font-black uppercase tracking-wider text-slate-500">Membership Details</h3>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Joined</p>
            <p class="mt-1 text-sm font-semibold text-slate-900">{{ formatDate(clubRequest?.experience?.joinDate) }}</p>
          </div>

          <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Category</p>
            <p class="mt-1 text-sm font-semibold text-slate-900">{{ clubRequest?.experience?.categoryType || 'N/A' }}</p>
          </div>

          <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Place</p>
            <p class="mt-1 text-sm font-semibold text-slate-900">{{ clubRequest?.experience?.place || club.ecole || 'N/A' }}</p>
          </div>

          <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Status</p>
            <p class="mt-1 text-sm font-semibold text-emerald-700">{{ clubRequest?.status || 'ACTIVE' }}</p>
          </div>
        </div>
      </div>
    </section>

    <section
      v-else-if="!playerClubStore.loading"
      class="rounded-3xl border border-dashed border-slate-200 bg-white px-6 py-14 text-center text-slate-500"
    >
      <p class="text-lg font-semibold text-slate-700">You are not currently in a club.</p>
      <p class="mt-1 text-sm text-slate-500">Accept a club request to see your active club here.</p>
    </section>
  </div>
</template>
