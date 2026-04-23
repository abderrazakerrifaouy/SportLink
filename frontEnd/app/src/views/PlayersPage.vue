<template>
  <div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Players List</h1>
      <div class="relative">
        <input
          v-model="searchInput"
          @input="onSearchInput"
          type="text"
          placeholder="Search by name, position, club..."
          class="w-64 rounded-lg border border-gray-300 py-2 pl-10 pr-4 transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 md:w-80"
        />
        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>
    </div>

    <div
      v-if="inviteFeedback"
      :class="inviteFeedback.type === 'success' ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700'"
      class="rounded-xl border p-4"
    >
      {{ inviteFeedback.message }}
    </div>

    <div v-if="loading" class="flex flex-col items-center justify-center py-12">
      <div class="mb-4 h-10 w-10 animate-spin rounded-full border-b-2 border-indigo-600"></div>
      <p class="text-gray-500">Chargement des joueurs...</p>
    </div>

    <div v-else-if="error" class="flex items-center rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
      <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
      </svg>
      {{ error }}
    </div>

    <div v-else-if="filteredPlayers.length === 0" class="rounded-2xl border border-gray-100 bg-white p-12 text-center shadow-sm">
      <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gray-50">
        <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
      </div>
      <h3 class="text-lg font-medium text-gray-900">Aucun joueur trouvé</h3>
      <p class="mt-1 text-gray-500">{{ searchInput ? 'Essayez un autre mot-clé.' : 'La liste est vide pour le moment.' }}</p>
    </div>

    <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      <div
        v-for="player in filteredPlayers"
        :key="player.id"
        @click="goToPlayerDetails(player.id)"
        class="group cursor-pointer rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
      >
        <div class="flex items-center space-x-4">
          <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-full bg-indigo-50 ring-2 ring-transparent transition-all group-hover:ring-indigo-100">
            <img
              :src="player.user?.profileImage || '/default-avatar.png'"
              class="h-full w-full object-cover"
              @error="(e) => (e.target.style.display = 'none')"
            />
          </div>

          <div class="min-w-0 flex-1">
            <h3 class="truncate font-bold text-gray-900 transition-colors group-hover:text-indigo-600">
              {{ player.user?.name }}
            </h3>
            <p class="truncate text-xs text-gray-500">{{ player.user?.email }}</p>
          </div>
        </div>

        <div class="mt-5 flex items-center justify-between gap-2">
          <div class="flex items-center rounded-lg bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-600">
            <svg class="mr-1.5 h-3.5 w-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 001.75-2.75L16.5 14.5a2 2 0 00-2-2H7.5a2 2 0 00-2 2L3 17.25A2 2 0 005 20z" />
            </svg>
            {{ player.experiences?.length || 0 }} Experience{{ player.experiences?.length !== 1 ? 's' : '' }}
          </div>

          <button
            v-if="isClubAdmin"
            type="button"
            :disabled="!canSendRequest(player.id)"
            :class="canSendRequest(player.id) ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-slate-100 text-slate-500'"
            class="rounded-lg px-2.5 py-1.5 text-[11px] font-semibold transition disabled:cursor-not-allowed disabled:opacity-60"
            @click.stop="sendClubRequest(player.id)"
          >
            {{ requestButtonLabel(player.id) }}
          </button>

          <span v-else class="flex items-center text-xs font-semibold text-indigo-600 opacity-0 transition-opacity group-hover:opacity-100">
            Voir Profil
            <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useJoueurStore } from '@/stores/joueurStore'
import { useAuthStore } from '@/stores/AuthStore'
import clubAdminService from '@/services/cloubAdminService'
import clubRequestService from '@/services/clubRequestService'

const router = useRouter()
const joueurStore = useJoueurStore()
const authStore = useAuthStore()

const loading = ref(true)
const error = ref(null)
const searchInput = ref('')
const inviteFeedback = ref(null)
const sendingRequestPlayerIds = ref(new Set())
const requestStatusByPlayerId = ref({})

const filteredPlayers = computed(() => {
  return joueurStore.filteredJoueurs
})

const isClubAdmin = computed(() => {
  return String(authStore.user?.role || '').toUpperCase() === 'CLUB_ADMIN'
})

const onSearchInput = () => {
  joueurStore.setSearchQuery(searchInput.value)
}

const loadClubRequestStatuses = async () => {
  if (!isClubAdmin.value) {
    requestStatusByPlayerId.value = {}
    return
  }

  try {
    const response = await clubRequestService.getHistory('club-admin')
    const history = response.data?.data || response.data || []
    const nextStatus = {}

    for (const request of history) {
      const joueurId = request?.joueur_id
      if (!joueurId || nextStatus[joueurId]) {
        continue
      }
      nextStatus[joueurId] = request.status
    }

    requestStatusByPlayerId.value = nextStatus
  } catch {
    requestStatusByPlayerId.value = {}
  }
}

const fetchPlayers = async () => {
  loading.value = true
  error.value = null
  try {
    await Promise.all([
      joueurStore.fetchJoueurs(),
      loadClubRequestStatuses(),
    ])
  } catch (err) {
    error.value = 'Impossible de charger les joueurs. Veuillez reessayer.'
    console.error('Fetch Error:', err)
  } finally {
    loading.value = false
  }
}

const goToPlayerDetails = (playerId) => {
  router.push({ name: 'PlayerDetails', params: { id: playerId } })
}

const isSendingRequest = (playerId) => sendingRequestPlayerIds.value.has(playerId)

const latestRequestStatus = (playerId) => requestStatusByPlayerId.value[playerId] || null

const canSendRequest = (playerId) => {
  if (!isClubAdmin.value || isSendingRequest(playerId)) {
    return false
  }

  const status = latestRequestStatus(playerId)
  return status !== 'PENDING' && status !== 'ACCEPTED'
}

const requestButtonLabel = (playerId) => {
  if (isSendingRequest(playerId)) return 'Sending...'

  const status = latestRequestStatus(playerId)
  if (status === 'PENDING') return 'Pending'
  if (status === 'ACCEPTED') return 'Added'
  if (status === 'REJECTED') return 'Resend'

  return 'Send Request'
}

const sendClubRequest = async (playerId) => {
  if (!canSendRequest(playerId)) return

  inviteFeedback.value = null
  sendingRequestPlayerIds.value.add(playerId)

  try {
    await clubAdminService.invitePlayerToClub(playerId)
    requestStatusByPlayerId.value = {
      ...requestStatusByPlayerId.value,
      [playerId]: 'PENDING',
    }
    inviteFeedback.value = {
      type: 'success',
      message: 'Club request sent successfully.',
    }
  } catch (err) {
    inviteFeedback.value = {
      type: 'error',
      message: err?.response?.data?.message || 'Failed to send club request.',
    }
  } finally {
    sendingRequestPlayerIds.value.delete(playerId)
  }
}

onMounted(() => {
  fetchPlayers()
})
</script>
