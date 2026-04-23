<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between gap-3">
      <h1 class="text-2xl font-bold text-gray-900">My Titles</h1>
      <button
        type="button"
        @click="openAddTitle"
        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700"
      >
        <span class="text-base">+</span>
        <span>Add Title</span>
      </button>
    </div>

    <div v-if="feedback" :class="feedback.type === 'success' ? 'border-green-200 bg-green-50 text-green-700' : 'border-red-200 bg-red-50 text-red-700'" class="rounded-2xl border p-4">
      {{ feedback.message }}
    </div>

    <div v-if="loading" class="flex justify-center py-10">
      <div class="h-9 w-9 animate-spin rounded-full border-b-2 border-indigo-600"></div>
    </div>

    <div v-else-if="error" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
      {{ error }}
    </div>

    <div v-else-if="titles.length === 0" class="bg-white rounded-2xl p-8 text-center border border-dashed border-gray-200">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
      </svg>
      <p class="text-gray-700 font-medium">No titles yet</p>
      <p class="text-gray-500 text-sm mt-1">Click "Add Title" to create your first one.</p>
    </div>

    <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
      <article
        v-for="title in titles"
        :key="title.id"
        class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"
      >
        <div class="flex items-center justify-between gap-3">
          <h2 class="text-base font-bold text-gray-900">{{ title.nomTitre }}</h2>
          <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">
            x{{ title.number }}
          </span>
        </div>
        <p class="mt-3 text-sm leading-6 text-gray-600">{{ title.description }}</p>
      </article>
    </div>

    <div v-if="showAddForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="closeAddTitle">
      <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
        <div class="mb-5 flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900">Add New Title</h2>
          <button type="button" @click="closeAddTitle" class="rounded-full p-2 text-gray-500 transition hover:bg-gray-100">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form class="space-y-4" @submit.prevent="submitTitle">
          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Title Name</label>
            <input
              v-model="form.nomTitre"
              type="text"
              class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none transition focus:border-indigo-500"
              placeholder="UEFA Champions League"
              required
            >
          </div>

          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Description</label>
            <textarea
              v-model="form.description"
              rows="4"
              class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none transition focus:border-indigo-500"
              placeholder="Describe this achievement"
              required
            ></textarea>
          </div>

          <div>
            <label class="mb-1 block text-sm font-semibold text-gray-700">Number</label>
            <input
              v-model.number="form.number"
              type="number"
              min="1"
              class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none transition focus:border-indigo-500"
              placeholder="1"
              required
            >
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button
              type="button"
              @click="closeAddTitle"
              class="rounded-xl bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-200"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-70"
            >
              {{ submitting ? 'Adding...' : 'Add Title' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import clubAdminService from '@/services/cloubAdminService'
import { useAuthStore } from '@/stores/AuthStore'
import api from '@/api/api'

const authStore = useAuthStore()

const loading = ref(true)
const submitting = ref(false)
const error = ref(null)
const feedback = ref(null)
const showAddForm = ref(false)
const titles = ref([])
const clubAdminId = ref(null)

const initialForm = {
  nomTitre: '',
  description: '',
  number: 1
}

const form = ref({ ...initialForm })

const resetForm = () => {
  form.value = { ...initialForm }
}

const openAddTitle = () => {
  feedback.value = null
  showAddForm.value = true
}

const closeAddTitle = () => {
  showAddForm.value = false
  resetForm()
}

const fetchTitles = async () => {
  loading.value = true
  error.value = null

  try {
    let userId = authStore.user?.id

    if (!userId) {
      const meResponse = await api.get('/user/authenticated')
      userId = meResponse.data?.id
    }

    if (!userId) {
      throw new Error('Authenticated user not found.')
    }

    const clubAdminResponse = await clubAdminService.getClubAdminByUserId(userId)
    clubAdminId.value = clubAdminResponse.data?.id

    if (!clubAdminId.value) {
      titles.value = []
      return
    }

    const titlesResponse = await clubAdminService.getTitresByClubAdminId(clubAdminId.value)
    titles.value = Array.isArray(titlesResponse.data?.data) ? titlesResponse.data.data : []
  } catch (err) {
    if (err?.response?.status === 404) {
      titles.value = []
      return
    }

    error.value = 'Failed to load titles.'
  } finally {
    loading.value = false
  }
}

const submitTitle = async () => {
  if (submitting.value) return

  submitting.value = true
  feedback.value = null

  try {
    const payload = {
      nomTitre: form.value.nomTitre,
      description: form.value.description,
      number: Number(form.value.number)
    }

    const response = await clubAdminService.createTitre(payload)
    const createdTitle = response.data?.data || response.data

    if (createdTitle) {
      const existingIndex = titles.value.findIndex((title) => title.id === createdTitle.id)
      if (existingIndex !== -1) {
        titles.value[existingIndex] = createdTitle
      } else {
        titles.value.unshift(createdTitle)
      }
    } else {
      await fetchTitles()
    }

    feedback.value = {
      type: 'success',
      message: 'Title added successfully.'
    }

    closeAddTitle()
  } catch (err) {
    feedback.value = {
      type: 'error',
      message: err?.response?.data?.message || 'Failed to add title.'
    }
  } finally {
    submitting.value = false
  }
}

onMounted(async () => {
  await fetchTitles()
})
</script>
