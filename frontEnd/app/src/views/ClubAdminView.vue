<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useClubAdminStore } from '@/stores/clubAdminStore'
import { useAuthStore } from '@/stores/AuthStore'
import { storeToRefs } from 'pinia'
import MainLayout from '@/layouts/MainLayout.vue'

const route = useRoute()
const clubAdminStore = useClubAdminStore()
const authStore = useAuthStore()
const { user: currentUser } = storeToRefs(authStore)

const clubAdmin = ref(null)
const titres = ref([])
const loading = ref(true)
const showTitreForm = ref(false)
const editingTitre = ref(null)
const savingTitre = ref(false)
const deletingTitreId = ref(null)

const userId = computed(() => Number(route.params.userId))
const isOwner = computed(() => currentUser.value?.id === userId.value)

const titreForm = ref({ titre: '', description: '', year: '' })

const resetForm = () => {
  titreForm.value = { titre: '', description: '', year: '' }
  editingTitre.value = null
  showTitreForm.value = false
}

const loadData = async () => {
  loading.value = true
  try {
    clubAdmin.value = await clubAdminStore.fetchClubAdminByUserId(userId.value)
    titres.value = await clubAdminStore.fetchTitres(clubAdmin.value.id)
  } catch (err) {
    console.error('Failed to load club admin:', err)
  } finally {
    loading.value = false
  }
}

const startEditTitre = (titre) => {
  editingTitre.value = titre
  titreForm.value = {
    titre: titre.titre || '',
    description: titre.description || '',
    year: titre.year || '',
  }
  showTitreForm.value = true
}

const saveTitre = async () => {
  if (!titreForm.value.titre.trim()) return
  savingTitre.value = true
  try {
    if (editingTitre.value) {
      const updated = await clubAdminStore.updateTitre(editingTitre.value.id, titreForm.value)
      const idx = titres.value.findIndex((t) => t.id === editingTitre.value.id)
      if (idx !== -1) titres.value[idx] = updated
    } else {
      const created = await clubAdminStore.createTitre({
        ...titreForm.value,
        club_admin_id: clubAdmin.value.id,
      })
      titres.value.push(created)
    }
    resetForm()
  } catch (err) {
    console.error('Failed to save titre:', err)
  } finally {
    savingTitre.value = false
  }
}

const deleteTitre = async (id) => {
  if (!confirm('Supprimer ce titre ?')) return
  deletingTitreId.value = id
  try {
    await clubAdminStore.deleteTitre(id)
    titres.value = titres.value.filter((t) => t.id !== id)
  } catch (err) {
    console.error('Failed to delete titre:', err)
  } finally {
    deletingTitreId.value = null
  }
}

onMounted(loadData)
</script>

<template>
  <MainLayout>
    <div v-if="loading" class="animate-pulse space-y-4">
      <div class="h-48 bg-gray-200 rounded-3xl"></div>
      <div class="h-32 bg-white rounded-3xl border border-gray-100"></div>
    </div>

    <div v-else-if="clubAdmin" class="space-y-6">
      <!-- Club Profile Card -->
      <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="h-40 bg-gradient-to-br from-emerald-900 to-emerald-600 relative">
          <div class="absolute inset-0 flex items-center justify-center">
            <i class="fa-solid fa-shield-halved text-white/20 text-9xl"></i>
          </div>
        </div>
        <div class="p-6">
          <div class="flex items-end justify-between -mt-16 mb-4">
            <div class="h-24 w-24 bg-white rounded-3xl border-4 border-white shadow-xl flex items-center justify-center">
              <i class="fa-solid fa-shield-halved text-emerald-700 text-3xl"></i>
            </div>
            <div class="flex gap-3 mb-2">
              <RouterLink
                :to="`/users/${userId}`"
                class="px-4 py-2 rounded-xl text-sm font-bold border border-gray-200 text-gray-700 hover:bg-gray-50 transition no-underline"
              >
                Voir profil
              </RouterLink>
              <RouterLink
                v-if="isOwner"
                to="/settings"
                class="px-4 py-2 rounded-xl text-sm font-bold bg-emerald-700 text-white hover:bg-emerald-800 transition no-underline"
              >
                <i class="fa-regular fa-pen-to-square mr-2"></i>Modifier
              </RouterLink>
            </div>
          </div>

          <h1 class="text-2xl font-black text-gray-900">{{ clubAdmin.club_name || clubAdmin.name }}</h1>
          <p class="text-sm font-bold text-emerald-700 uppercase tracking-widest mt-0.5">Club Admin</p>

          <div class="flex flex-wrap gap-3 mt-4">
            <span v-if="clubAdmin.city" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 rounded-xl text-xs font-bold text-gray-700">
              <i class="fa-solid fa-location-dot text-emerald-600"></i>{{ clubAdmin.city }}
            </span>
            <span v-if="clubAdmin.league" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 rounded-xl text-xs font-bold text-gray-700">
              <i class="fa-solid fa-trophy text-amber-500"></i>{{ clubAdmin.league }}
            </span>
            <span v-if="clubAdmin.founded_year" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 rounded-xl text-xs font-bold text-gray-700">
              <i class="fa-solid fa-calendar text-emerald-600"></i>Fondé en {{ clubAdmin.founded_year }}
            </span>
          </div>

          <p v-if="clubAdmin.description" class="text-sm text-gray-600 mt-4 leading-relaxed max-w-xl">{{ clubAdmin.description }}</p>
        </div>
      </div>

      <!-- Titles & Achievements -->
      <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-black text-gray-900">
            <i class="fa-solid fa-trophy text-amber-500 mr-2"></i>Palmarès
          </h2>
          <button
            v-if="isOwner"
            @click="showTitreForm = true; editingTitre = null; resetForm()"
            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold bg-emerald-700 text-white hover:bg-emerald-800 transition"
          >
            <i class="fa-solid fa-plus"></i> Ajouter
          </button>
        </div>

        <!-- Form -->
        <div v-if="showTitreForm" class="mb-6 bg-amber-50 rounded-2xl p-5 border border-amber-100">
          <h3 class="font-bold text-gray-900 mb-4">
            {{ editingTitre ? 'Modifier le titre' : 'Nouveau titre' }}
          </h3>
          <div class="space-y-3">
            <div>
              <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 block">Titre *</label>
              <input
                v-model="titreForm.titre"
                type="text"
                placeholder="Champion de Ligue 1"
                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
              />
            </div>
            <div>
              <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 block">Année</label>
              <input
                v-model="titreForm.year"
                type="number"
                placeholder="2024"
                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
              />
            </div>
            <div>
              <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 block">Description</label>
              <textarea
                v-model="titreForm.description"
                placeholder="Détails sur ce titre..."
                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 resize-none h-20"
              ></textarea>
            </div>
          </div>
          <div class="flex gap-3 mt-4">
            <button @click="resetForm" class="flex-1 py-2.5 rounded-xl text-sm font-bold border border-gray-200 text-gray-700 hover:bg-white transition">Annuler</button>
            <button
              @click="saveTitre"
              :disabled="savingTitre || !titreForm.titre"
              class="flex-1 py-2.5 rounded-xl text-sm font-bold bg-amber-500 text-white hover:bg-amber-600 transition disabled:opacity-50"
            >
              <i v-if="savingTitre" class="fa-solid fa-spinner animate-spin mr-2"></i>
              {{ editingTitre ? 'Sauvegarder' : 'Ajouter' }}
            </button>
          </div>
        </div>

        <!-- Titles list -->
        <div v-if="titres.length === 0 && !showTitreForm" class="text-center py-10">
          <i class="fa-solid fa-trophy text-gray-200 text-5xl mb-3"></i>
          <p class="text-gray-500 font-medium">Aucun titre enregistré</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="titre in titres"
            :key="titre.id"
            class="group flex items-start gap-4 p-4 bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-100 hover:border-amber-300 transition-colors"
          >
            <div class="w-12 h-12 bg-amber-400 rounded-2xl flex items-center justify-center shrink-0 shadow-sm">
              <i class="fa-solid fa-trophy text-white"></i>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="font-black text-gray-900">{{ titre.titre }}</h4>
              <p v-if="titre.year" class="text-xs font-bold text-amber-700 mt-0.5">{{ titre.year }}</p>
              <p v-if="titre.description" class="text-xs text-gray-600 mt-1">{{ titre.description }}</p>
            </div>
            <div v-if="isOwner" class="flex gap-1 opacity-0 group-hover:opacity-100 transition">
              <button @click="startEditTitre(titre)" class="p-2 rounded-xl hover:bg-amber-100 text-amber-700 transition">
                <i class="fa-regular fa-pen-to-square text-xs"></i>
              </button>
              <button
                @click="deleteTitre(titre.id)"
                :disabled="deletingTitreId === titre.id"
                class="p-2 rounded-xl hover:bg-red-100 text-red-500 transition disabled:opacity-50"
              >
                <i class="fa-regular fa-trash-can text-xs"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Not found -->
    <div v-else class="bg-white rounded-3xl border border-gray-100 p-10 text-center shadow-sm">
      <i class="fa-solid fa-shield-halved text-gray-300 text-5xl mb-4"></i>
      <h3 class="font-bold text-gray-800 mb-2">Club introuvable</h3>
      <RouterLink to="/" class="text-emerald-700 font-semibold text-sm hover:underline">Retour à l'accueil</RouterLink>
    </div>
  </MainLayout>
</template>
