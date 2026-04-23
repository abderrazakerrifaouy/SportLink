<template>
  <div class="space-y-8">
    <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 sm:p-8">
      <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div class="max-w-2xl">
          <p class="text-xs font-bold uppercase tracking-[0.32em] text-indigo-600">Clubs</p>
          <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Find a club at a glance</h1>
          <p class="mt-3 text-sm leading-6 text-slate-500 sm:text-base">
            View only the essential information here. Open a club to see full players, titles, posts, and profile details.
          </p>
        </div>

        <div class="relative w-full lg:w-[420px]">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search clubs..."
            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 pl-11 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10"
          />
          <svg class="absolute left-4 top-3.5 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>
    </section>

    <div v-if="loading" class="flex justify-center py-14">
      <div class="h-10 w-10 animate-spin rounded-full border-b-2 border-indigo-600"></div>
    </div>

    <div v-else-if="error" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
      {{ error }}
    </div>

    <div v-else-if="filteredClubs.length === 0" class="rounded-3xl border border-dashed border-slate-200 bg-white p-12 text-center shadow-sm">
      <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-50 text-slate-300">
        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
      </div>
      <p class="text-lg font-semibold text-slate-900">No clubs found</p>
      <p class="mt-2 text-sm text-slate-500">Try a different search term.</p>
    </div>

    <div v-else class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
      <article
        v-for="club in filteredClubs"
        :key="club.id"
        class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md"
      >
        <div class="flex items-center gap-4 border-b border-slate-100 px-5 py-5">
          <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-indigo-600 text-2xl font-black text-white shadow-sm">
            {{ club.nomClub?.charAt(0) || 'C' }}
          </div>

          <div class="min-w-0 flex-1">
            <h2 class="truncate text-lg font-bold tracking-tight text-slate-900">{{ club.nomClub }}</h2>
            <p class="mt-1 line-clamp-2 text-sm leading-6 text-slate-500">
              {{ club.description || 'No description provided.' }}
            </p>
          </div>
        </div>

        <div class="px-5 pb-5 pt-4">
          <div class="flex items-center justify-between gap-3 text-xs font-semibold text-slate-500">
            <div class="flex flex-wrap gap-2">
              <span class="rounded-full bg-slate-100 px-2.5 py-1">{{ club.user?.name || 'Unknown admin' }}</span>
              <span class="rounded-full bg-slate-100 px-2.5 py-1">{{ club.ecole || 'No school' }}</span>
            </div>
            <button
              type="button"
              @click="goToClubDetails(club.id)"
              class="inline-flex items-center rounded-xl bg-indigo-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700"
            >
              View details
            </button>
          </div>
        </div>
      </article>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import clubAdminService from '@/services/cloubAdminService'

const router = useRouter()
const clubs = ref([])
const loading = ref(true)
const error = ref(null)
const searchQuery = ref('')

const filteredClubs = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()
  if (!query) return clubs.value

  return clubs.value.filter((club) => {
    const clubMatches = [club.nomClub, club.description, club.ecole, club.tactique, club.gestion]
      .filter(Boolean)
      .some((value) => value.toLowerCase().includes(query))

    const playerMatches = club.joueurs?.some((player) => {
      const name = player.user?.name?.toLowerCase() || ''
      const email = player.user?.email?.toLowerCase() || ''
      return name.includes(query) || email.includes(query)
    })

    const titleMatches = club.titres?.some((title) => {
      const titleName = (title.nomTitre || title.name || '').toLowerCase()
      const description = (title.description || '').toLowerCase()
      return titleName.includes(query) || description.includes(query)
    })

    const postMatches = club.posts?.some((post) => {
      const content = (post.content || '').toLowerCase()
      const author = (post.author?.name || '').toLowerCase()
      return content.includes(query) || author.includes(query)
    })

    return clubMatches || playerMatches || titleMatches || postMatches
  })
})

const fetchClubs = async () => {
  loading.value = true
  error.value = null

  try {
    const response = await clubAdminService.getClubAdmins()
    clubs.value = response.data
  } catch (err) {
    error.value = 'Failed to load clubs'
    console.error(err)
  } finally {
    loading.value = false
  }
}

const goToClubDetails = (clubId) => {
  router.push({ name: 'ClubDetails', params: { id: clubId } })
}

onMounted(fetchClubs)
</script>
