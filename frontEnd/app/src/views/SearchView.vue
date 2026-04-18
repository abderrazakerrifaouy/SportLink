<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUserStore } from '@/stores/userStore'
import MainLayout from '@/layouts/MainLayout.vue'

const route = useRoute()
const router = useRouter()
const userStore = useUserStore()

const query = ref(route.query.q || '')
const results = ref([])
const loading = ref(false)

let debounceTimer = null

const search = async (q) => {
  if (!q.trim()) {
    results.value = []
    return
  }
  loading.value = true
  try {
    results.value = await userStore.searchUsers(q.trim())
  } catch (err) {
    console.error('Search error:', err)
    results.value = []
  } finally {
    loading.value = false
  }
}

watch(query, (val) => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    router.replace({ path: '/search', query: val ? { q: val } : {} })
    search(val)
  }, 400)
})

watch(
  () => route.query.q,
  (val) => {
    if (val !== query.value) {
      query.value = val || ''
    }
  }
)

const avatarUrl = (name, img) =>
  img || `https://ui-avatars.com/api/?name=${encodeURIComponent(name || 'U')}&bg=064E3B&color=fff`

onMounted(() => {
  if (query.value) search(query.value)
})
</script>

<template>
  <MainLayout>
    <div class="max-w-2xl mx-auto space-y-6">
      <!-- Search header -->
      <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <h1 class="text-xl font-black text-gray-900 mb-4">
          <i class="fa-solid fa-magnifying-glass text-emerald-600 mr-2"></i>Recherche
        </h1>
        <div class="relative">
          <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
          <input
            v-model="query"
            type="text"
            placeholder="Rechercher des athlètes, clubs..."
            class="w-full bg-gray-50 border border-gray-200 rounded-2xl py-3 pl-12 pr-4 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none"
            autofocus
          />
          <button
            v-if="query"
            @click="query = ''"
            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
          >
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="space-y-3">
        <div v-for="n in 5" :key="n" class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm animate-pulse flex gap-3">
          <div class="h-14 w-14 bg-gray-200 rounded-2xl shrink-0"></div>
          <div class="flex-1 space-y-2 pt-1">
            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
            <div class="h-3 bg-gray-100 rounded w-1/4"></div>
          </div>
        </div>
      </div>

      <!-- Results -->
      <div v-else-if="results.length > 0" class="space-y-3">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest px-1">
          {{ results.length }} résultat{{ results.length > 1 ? 's' : '' }}
        </p>
        <div
          v-for="result in results"
          :key="result.id"
          class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center justify-between hover:shadow-md transition cursor-pointer"
          @click="router.push(`/users/${result.id}`)"
        >
          <div class="flex items-center gap-4">
            <img
              :src="avatarUrl(result.name, result.profileImage)"
              class="h-14 w-14 rounded-2xl object-cover border border-gray-100"
              :alt="result.name"
            />
            <div>
              <h3 class="font-bold text-gray-900">{{ result.name }}</h3>
              <p class="text-xs text-emerald-600 font-bold uppercase tracking-widest">{{ result.role }}</p>
              <p v-if="result.bio" class="text-xs text-gray-500 mt-0.5 max-w-xs truncate">{{ result.bio }}</p>
            </div>
          </div>
          <div class="flex gap-2">
            <RouterLink
              :to="`/users/${result.id}`"
              class="px-4 py-2 rounded-xl text-xs font-bold border border-gray-200 text-gray-700 hover:bg-gray-50 transition no-underline"
              @click.stop
            >
              Voir profil
            </RouterLink>
          </div>
        </div>
      </div>

      <!-- Empty -->
      <div v-else-if="query && !loading" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-10 text-center">
        <i class="fa-solid fa-user-slash text-gray-300 text-5xl mb-4"></i>
        <h3 class="font-bold text-gray-800 mb-2">Aucun résultat</h3>
        <p class="text-sm text-gray-500">Essayez un autre nom ou terme de recherche.</p>
      </div>

      <!-- Initial state -->
      <div v-else-if="!query" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-10 text-center">
        <i class="fa-solid fa-magnifying-glass text-gray-300 text-5xl mb-4"></i>
        <h3 class="font-bold text-gray-800 mb-2">Commencez à rechercher</h3>
        <p class="text-sm text-gray-500">Tapez le nom d'un athlète ou d'un club pour le trouver.</p>
      </div>
    </div>
  </MainLayout>
</template>
