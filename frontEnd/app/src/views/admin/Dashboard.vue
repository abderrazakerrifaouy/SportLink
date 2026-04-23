<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
    <div v-if="isLoading" class="flex justify-center py-10">
      <div class="h-8 w-8 animate-spin rounded-full border-b-2 border-indigo-600"></div>
    </div>

    <div v-else-if="!hasClubAdmin" class="bg-white rounded-2xl border border-dashed border-gray-200 p-10 text-center">
      <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-50 text-gray-300">
        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
      </div>
      <p class="text-lg font-semibold text-gray-900">No Club Admin created yet</p>
      <p class="mt-2 text-sm text-gray-500">Create and assign a Club Admin account to unlock dashboard data.</p>

      <div class="mt-8 rounded-2xl border border-gray-100 bg-gray-50/70 p-5 text-left">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Steps</p>
        <ol class="mt-3 space-y-2 text-sm text-gray-700">
          <li class="flex items-start gap-2">
            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700">1</span>
            <span>Click <strong>Create Club Admin</strong>.</span>
          </li>
          <li class="flex items-start gap-2">
            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700">2</span>
            <span>Fill in club information (name, description, school, tactics, management).</span>
          </li>
          <li class="flex items-start gap-2">
            <span class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700">3</span>
            <span>Submit and the dashboard will be unlocked automatically.</span>
          </li>
        </ol>
      </div>

      <button
        v-if="!showCreateForm"
        @click="showCreateForm = true"
        class="mt-6 inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700"
      >
        Create Club Admin
      </button>

      <form v-else class="mt-6 rounded-2xl border border-gray-100 p-5 text-left" @submit.prevent="submitCreateClubAdmin">
        <p class="mb-4 text-sm font-semibold text-gray-800">Create Club Admin</p>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="md:col-span-2">
            <label class="mb-1 block text-xs font-medium text-gray-600">Club name</label>
            <input v-model="createForm.nomClub" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" />
          </div>

          <div class="md:col-span-2">
            <label class="mb-1 block text-xs font-medium text-gray-600">Description</label>
            <textarea v-model="createForm.description" required rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"></textarea>
          </div>

          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">School</label>
            <input v-model="createForm.ecole" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" />
          </div>

          <div>
            <label class="mb-1 block text-xs font-medium text-gray-600">Tactics</label>
            <input v-model="createForm.tactique" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" />
          </div>

          <div class="md:col-span-2">
            <label class="mb-1 block text-xs font-medium text-gray-600">Management</label>
            <input v-model="createForm.gestion" type="text" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none" />
          </div>
        </div>

        <p v-if="createError" class="mt-3 text-sm text-red-600">{{ createError }}</p>

        <div class="mt-5 flex items-center justify-end gap-2">
          <button
            type="button"
            @click="cancelCreateClubAdmin"
            class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="isCreatingClubAdmin"
            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
          >
            {{ isCreatingClubAdmin ? 'Creating...' : 'Create' }}
          </button>
        </div>
      </form>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <p class="text-3xl font-bold text-blue-600">{{ stats.totalUsers }}</p>
        <p class="text-gray-500">Total Users</p>
      </div>
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <p class="text-3xl font-bold text-green-600">{{ stats.totalPosts }}</p>
        <p class="text-gray-500">Total Posts</p>
      </div>
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <p class="text-3xl font-bold text-purple-600">{{ stats.totalPlayers }}</p>
        <p class="text-gray-500">Players</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useAuthStore } from '@/stores/AuthStore'
import { useUserStore } from '@/stores/userStore'
import { usePostStore } from '@/stores/PostStore'
import { useClubAdminStore } from '@/stores/clubAdminStore'

const authStore = useAuthStore()
const userStore = useUserStore()
const postStore = usePostStore()
const clubAdminStore = useClubAdminStore()

const stats = ref({
  totalUsers: 0,
  totalPosts: 0,
  totalPlayers: 0
})

const isLoading = ref(true)
const hasClubAdmin = computed(() => Boolean(clubAdminStore.currentClubAdmin?.id))
const showCreateForm = ref(false)
const isCreatingClubAdmin = ref(false)
const createError = ref('')
const createForm = ref({
  nomClub: '',
  description: '',
  ecole: '',
  tactique: '',
  gestion: '',
})

const fetchStats = async () => {
  if (!hasClubAdmin.value) {
    return
  }

  try {
    await userStore.fetchUsers()
    stats.value.totalUsers = userStore.users.length
    stats.value.totalPlayers = userStore.users.filter((user) => {
      const role = user.role?.toLowerCase() || ''
      return role.includes('player') || role.includes('joueur')
    }).length
    await postStore.fetchPosts()
    stats.value.totalPosts = postStore.posts.length
  } catch (error) {
    console.error('Failed to fetch stats:', error)
  }
}

const resolveClubAdmin = async (userId) => {
  isLoading.value = true

  if (!userId) {
    clubAdminStore.currentClubAdmin = null
    isLoading.value = false
    return
  }

  try {
    const exists = await clubAdminStore.clubAdminExists(userId)
    if (exists) {
      await fetchStats()
    } else {
      clubAdminStore.currentClubAdmin = null
    }
  } catch (error) {
    clubAdminStore.currentClubAdmin = null
    if (error?.response?.status !== 404) {
      console.error('Failed to resolve club admin account:', error)
    }
  } finally {
    isLoading.value = false
  }
}

const resetCreateForm = () => {
  createForm.value = {
    nomClub: '',
    description: '',
    ecole: '',
    tactique: '',
    gestion: '',
  }
}

const cancelCreateClubAdmin = () => {
  createError.value = ''
  showCreateForm.value = false
  resetCreateForm()
}

const submitCreateClubAdmin = async () => {
  createError.value = ''
  isCreatingClubAdmin.value = true

  try {
    await clubAdminStore.createClubAdmin(createForm.value)
    cancelCreateClubAdmin()
    await resolveClubAdmin(authStore.user?.id)
  } catch (error) {
    createError.value = error?.response?.data?.message || 'Failed to create Club Admin. Please try again.'
  } finally {
    isCreatingClubAdmin.value = false
  }
}

watch(
  () => authStore.user?.id,
  (userId) => {
    console.log('Auth user ID changed:', userId)
    resolveClubAdmin(userId)
  },
  { immediate: true }
)
</script>
