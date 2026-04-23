<script setup>
import { computed, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import clubRequestService from '@/services/clubRequestService'
import { useJoueurStore } from '@/stores/joueurStore'

const route = useRoute()
const router = useRouter()
const joueurStore = useJoueurStore()

const loadingContext = ref(false)
const submitting = ref(false)
const error = ref(null)
const success = ref(null)
const pendingRequest = ref(null)

const today = new Date().toISOString().slice(0, 10)

const form = ref({
  idClub: '',
  image: '',
  joinDate: today,
  endDate: '',
  place: '',
  categoryType: 'SENIOR',
})

const categoryOptions = ['SENIOR', 'ESPOIR', 'JUNIOR', 'CADET', 'MINIM']

const isRequestFlow = computed(() => Boolean(pendingRequest.value?.requestId))

const canSubmit = computed(() => {
  return Boolean(form.value.idClub && form.value.place && form.value.categoryType) && !submitting.value
})

const applyPrefillFromRequest = (requestPayload) => {
  if (!requestPayload) {
    pendingRequest.value = null
    return
  }

  pendingRequest.value = requestPayload
  form.value.idClub = requestPayload.clubId ? String(requestPayload.clubId) : ''
  form.value.image = requestPayload.clubImage || ''
  form.value.place = requestPayload.place || ''
  form.value.categoryType = requestPayload.categoryType || 'SENIOR'
}

const findRequestFromApi = async (requestId) => {
  const response = await clubRequestService.getHistory('player')
  const history = response.data?.data || response.data || []
  const target = history.find((item) => String(item.id) === String(requestId))

  if (!target) {
    return null
  }

  return {
    requestId: target.id,
    clubId: target.club_admin_id,
    clubName: target.club?.nomClub || 'Unknown club',
    clubImage: target.club?.user?.profileImage || null,
    place: target.club?.ecole || target.club?.nomClub || '',
    categoryType: 'SENIOR',
  }
}

const loadContext = async () => {
  loadingContext.value = true
  error.value = null

  try {
    const queryRequestId = route.query.requestId
    const storePayload = joueurStore.pendingExperienceRequest

    if (storePayload && (!queryRequestId || String(storePayload.requestId) === String(queryRequestId))) {
      applyPrefillFromRequest(storePayload)
      return
    }

    if (queryRequestId) {
      const fetchedPayload = await findRequestFromApi(queryRequestId)
      if (fetchedPayload) {
        joueurStore.setPendingExperienceRequest(fetchedPayload)
        applyPrefillFromRequest(fetchedPayload)
      }
    }
  } catch (err) {
    error.value = err?.response?.data?.message || 'Unable to preload request details.'
  } finally {
    loadingContext.value = false
  }
}

const submitExperience = async () => {
  if (!canSubmit.value) return

  submitting.value = true
  error.value = null
  success.value = null

  try {
    const payload = {
      idClub: form.value.idClub,
      image: form.value.image || null,
      joinDate: form.value.joinDate || null,
      endDate: form.value.endDate || null,
      place: form.value.place,
      categoryType: form.value.categoryType,
    }

    const createdExperience = await joueurStore.createExperience(payload)

    if (isRequestFlow.value) {
      await clubRequestService.acceptRequest(pendingRequest.value.requestId, {
        experience_id: createdExperience.id,
      })

      joueurStore.clearPendingExperienceRequest()
      success.value = 'Experience created and request accepted successfully.'
      router.push({ name: 'PlayerClubRequests' })
      return
    }

    success.value = 'Experience created successfully.'
    form.value.endDate = ''
  } catch (err) {
    error.value = err?.response?.data?.message || 'Failed to create experience.'
  } finally {
    submitting.value = false
  }
}

watch(
  () => route.fullPath,
  () => {
    loadContext()
  },
  { immediate: true }
)
</script>

<template>
  <div class="space-y-6">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-2">
        <p class="text-xs font-bold uppercase tracking-[0.32em] text-indigo-600">Player Experience</p>
        <h1 class="text-3xl font-black tracking-tight text-slate-900">
          {{ isRequestFlow ? 'Create Experience to Accept Request' : 'Create New Experience' }}
        </h1>
        <p class="max-w-2xl text-sm leading-6 text-slate-500">
          Fill in your experience details. If this comes from a club request, submitting this form will also mark the request as accepted.
        </p>
      </div>
    </section>

    <div v-if="loadingContext" class="flex justify-center py-10">
      <div class="h-10 w-10 animate-spin rounded-full border-b-2 border-indigo-600"></div>
    </div>

    <div v-else class="space-y-4">
      <section
        v-if="pendingRequest"
        class="rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4 text-sm text-indigo-800"
      >
        <p class="font-semibold">Request selected: {{ pendingRequest.clubName }}</p>
        <p class="mt-1">This experience will be linked to your invitation and finalize acceptance.</p>
      </section>

      <div v-if="error" class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-700">
        {{ error }}
      </div>

      <div v-if="success" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700">
        {{ success }}
      </div>

      <form @submit.prevent="submitExperience" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Club ID</label>
            <input
              v-model="form.idClub"
              type="number"
              min="1"
              required
              class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-indigo-400"
            />
          </div>

          <div>
            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Category</label>
            <select
              v-model="form.categoryType"
              required
              class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-indigo-400"
            >
              <option v-for="option in categoryOptions" :key="option" :value="option">{{ option }}</option>
            </select>
          </div>

          <div>
            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Join Date</label>
            <input
              v-model="form.joinDate"
              type="date"
              class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-indigo-400"
            />
          </div>

          <div>
            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">End Date</label>
            <input
              v-model="form.endDate"
              type="date"
              class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-indigo-400"
            />
          </div>

          <div class="md:col-span-2">
            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Place</label>
            <input
              v-model="form.place"
              type="text"
              required
              placeholder="Club location or school"
              class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-indigo-400"
            />
          </div>

          <div class="md:col-span-2">
            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Image URL (optional)</label>
            <input
              v-model="form.image"
              type="text"
              placeholder="https://..."
              class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-indigo-400"
            />
          </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
          <button
            type="submit"
            :disabled="!canSubmit"
            class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
          >
            {{ submitting ? 'Saving...' : (isRequestFlow ? 'Create Experience & Accept' : 'Create Experience') }}
          </button>

          <button
            v-if="isRequestFlow"
            type="button"
            @click="router.push({ name: 'PlayerClubRequests' })"
            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-300"
          >
            Back to Requests
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
