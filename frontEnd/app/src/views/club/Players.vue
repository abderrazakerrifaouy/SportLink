<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">My Players</h1>
      <span v-if="!loading && !error" class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">
        {{ players.length }} players
      </span>
    </div>

    <div v-if="inviteFeedback" :class="inviteFeedback.type === 'success' ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700'" class="rounded-2xl border p-4">
      {{ inviteFeedback.message }}
    </div>

    <div v-if="loading" class="flex justify-center py-10">
      <div class="h-9 w-9 animate-spin rounded-full border-b-2 border-indigo-600"></div>
    </div>

    <div v-else-if="error" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
      {{ error }}
    </div>

    <div v-else-if="players.length === 0" class="bg-white rounded-2xl p-10 text-center border border-dashed border-gray-200">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
      <p class="text-gray-700 font-medium">No players in your club yet</p>
      <p class="text-gray-500 text-sm mt-1">Players will appear here once requests are accepted.</p>
    </div>

    <div v-else class="space-y-8">
      <section>
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Club Players</h2>
        <div v-if="players.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          <article
            v-for="player in players"
            :key="player.id"
            class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition cursor-pointer"
            @click="goToPlayer(player.id)"
          >
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-full overflow-hidden bg-indigo-100 flex items-center justify-center shrink-0">
                <img
                  v-if="player.user?.profile_photo_path"
                  :src="player.user.profile_photo_path"
                  alt=""
                  class="w-full h-full object-cover"
                />
                <span v-else class="font-bold text-indigo-700">{{ player.user?.name?.charAt(0) || 'P' }}</span>
              </div>
              <div class="min-w-0">
                <p class="font-semibold text-gray-900 truncate">{{ player.user?.name || 'Unknown player' }}</p>
                <p class="text-sm text-gray-500 truncate">{{ player.user?.email || 'No email' }}</p>
              </div>
            </div>
          </article>
        </div>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Invite Players To Club</h2>
        <div v-if="availablePlayers.length === 0" class="rounded-2xl border border-dashed border-gray-200 bg-white p-6 text-sm text-gray-600">
          No available players to invite right now.
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          <article
            v-for="player in availablePlayers"
            :key="player.id"
            class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm"
          >
            <div class="flex items-center justify-between gap-3">
              <div class="min-w-0">
                <p class="font-semibold text-gray-900 truncate">{{ player.user?.name || 'Unknown player' }}</p>
                <p class="text-sm text-gray-500 truncate">{{ player.user?.email || 'No email' }}</p>
              </div>
              <button
                class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                :disabled="invitingPlayerIds.has(player.id) || invitedPlayerIds.has(player.id)"
                @click="invitePlayer(player.id)"
              >
                {{ invitingPlayerIds.has(player.id) ? 'Sending...' : invitedPlayerIds.has(player.id) ? 'Sent' : 'Invite' }}
              </button>
            </div>
          </article>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/AuthStore'
import clubAdminService from '@/services/cloubAdminService'
import joueurService from '@/services/joueurService'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(true)
const error = ref(null)
const players = ref([])
const availablePlayers = ref([])
const invitingPlayerIds = ref(new Set())
const invitedPlayerIds = ref(new Set())
const inviteFeedback = ref(null)

const fetchPlayers = async (userId) => {
  if (!userId) {
    loading.value = false
    players.value = []
    availablePlayers.value = []
    return
  }

  loading.value = true
  error.value = null

  try {
    const [clubAdminResponse, joueursResponse] = await Promise.all([
      clubAdminService.getClubAdminByUserId(userId),
      joueurService.getAllJoueurs(),
    ])

    players.value = clubAdminResponse.data?.joueurs || []

    const currentClubPlayerIds = new Set(players.value.map((player) => player.id))
    const allJoueurs = joueursResponse.data?.data || []
    availablePlayers.value = allJoueurs.filter((player) => !currentClubPlayerIds.has(player.id))
  } catch (err) {
    players.value = []
    availablePlayers.value = []
    error.value = err?.response?.status === 404
      ? 'No Club Admin profile found for this account.'
      : 'Failed to load club players.'
  } finally {
    loading.value = false
  }
}

const invitePlayer = async (playerId) => {
  if (invitingPlayerIds.value.has(playerId)) {
    return
  }

  inviteFeedback.value = null
  invitingPlayerIds.value.add(playerId)

  try {
    await clubAdminService.invitePlayerToClub(playerId)
    invitedPlayerIds.value.add(playerId)
    inviteFeedback.value = {
      type: 'success',
      message: 'Invitation request sent successfully.',
    }
  } catch (err) {
    inviteFeedback.value = {
      type: 'error',
      message: err?.response?.data?.message || 'Failed to send invitation request.',
    }
  } finally {
    invitingPlayerIds.value.delete(playerId)
  }
}

const goToPlayer = (playerId) => {
  router.push({ name: 'PlayerDetails', params: { id: playerId } })
}

watch(
  () => authStore.user?.id,
  (userId) => {
    fetchPlayers(userId)
  },
  { immediate: true }
)
</script>
