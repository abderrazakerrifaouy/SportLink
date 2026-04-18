<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useJoueurStore } from '@/stores/joueurStore'
import { useAuthStore } from '@/stores/AuthStore'
import { storeToRefs } from 'pinia'
import MainLayout from '@/layouts/MainLayout.vue'

const route = useRoute()
const joueurStore = useJoueurStore()
const authStore = useAuthStore()
const { user: currentUser } = storeToRefs(authStore)

const joueur = ref(null)
const experiences = ref([])
const loading = ref(true)
const showExpForm = ref(false)
const editingExp = ref(null)
const savingExp = ref(false)
const deletingExpId = ref(null)

const joueurId = computed(() => Number(route.params.id))
const isOwner = computed(() => currentUser.value?.id === joueur.value?.user_id)

const expForm = ref({
  club: '',
  position: '',
  start_date: '',
  end_date: '',
  description: '',
})

const resetForm = () => {
  expForm.value = { club: '', position: '', start_date: '', end_date: '', description: '' }
  editingExp.value = null
  showExpForm.value = false
}

const loadData = async () => {
  loading.value = true
  try {
    joueur.value = await joueurStore.fetchJoueur(joueurId.value)
    experiences.value = await joueurStore.fetchExperiences(joueurId.value)
  } catch (err) {
    console.error('Failed to load joueur:', err)
  } finally {
    loading.value = false
  }
}

const startEditExp = (exp) => {
  editingExp.value = exp
  expForm.value = {
    club: exp.club || '',
    position: exp.position || '',
    start_date: exp.start_date?.split('T')[0] || '',
    end_date: exp.end_date?.split('T')[0] || '',
    description: exp.description || '',
  }
  showExpForm.value = true
}

const saveExperience = async () => {
  savingExp.value = true
  try {
    if (editingExp.value) {
      const updated = await joueurStore.updateExperience(editingExp.value.id, expForm.value)
      const idx = experiences.value.findIndex((e) => e.id === editingExp.value.id)
      if (idx !== -1) experiences.value[idx] = updated
    } else {
      const created = await joueurStore.createExperience({
        ...expForm.value,
        joueur_id: joueurId.value,
      })
      experiences.value.push(created)
    }
    resetForm()
  } catch (err) {
    console.error('Failed to save experience:', err)
  } finally {
    savingExp.value = false
  }
}

const deleteExperience = async (id) => {
  if (!confirm('Supprimer cette expérience ?')) return
  deletingExpId.value = id
  try {
    await joueurStore.deleteExperience(id)
    experiences.value = experiences.value.filter((e) => e.id !== id)
  } catch (err) {
    console.error('Failed to delete experience:', err)
  } finally {
    deletingExpId.value = null
  }
}

const formatPeriod = (start, end) => {
  const s = start ? new Date(start).getFullYear() : '?'
  const e = end ? new Date(end).getFullYear() : 'Présent'
  return `${s} – ${e}`
}

onMounted(loadData)
</script>

<template>
  <MainLayout>
    <div v-if="loading" class="animate-pulse space-y-4">
      <div class="h-48 bg-gray-200 rounded-3xl"></div>
      <div class="h-32 bg-white rounded-3xl border border-gray-100"></div>
    </div>

    <div v-else-if="joueur" class="space-y-6">
      <!-- Profile Card -->
      <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
        <div class="flex items-start gap-6">
          <div class="h-24 w-24 bg-emerald-100 rounded-3xl flex items-center justify-center shrink-0">
            <i class="fa-solid fa-futbol text-emerald-600 text-3xl"></i>
          </div>
          <div class="flex-1">
            <div class="flex items-start justify-between">
              <div>
                <h1 class="text-2xl font-black text-gray-900">{{ joueur.name || joueur.user?.name }}</h1>
                <p class="text-sm font-bold text-emerald-700 uppercase tracking-widest mt-0.5">Joueur</p>
                <div class="flex flex-wrap gap-3 mt-3">
                  <span v-if="joueur.nationality" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 rounded-xl text-xs font-bold text-gray-700">
                    <i class="fa-solid fa-flag text-emerald-600"></i>{{ joueur.nationality }}
                  </span>
                  <span v-if="joueur.position" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 rounded-xl text-xs font-bold text-gray-700">
                    <i class="fa-solid fa-shirt text-emerald-600"></i>{{ joueur.position }}
                  </span>
                  <span v-if="joueur.date_of_birth" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 rounded-xl text-xs font-bold text-gray-700">
                    <i class="fa-solid fa-cake-candles text-emerald-600"></i>{{ new Date(joueur.date_of_birth).getFullYear() }}
                  </span>
                </div>
              </div>
              <RouterLink
                :to="`/users/${joueur.user_id || joueur.user?.id}`"
                class="px-4 py-2 rounded-xl text-sm font-bold border border-gray-200 text-gray-700 hover:bg-gray-50 transition no-underline"
              >
                Voir profil
              </RouterLink>
            </div>
            <p v-if="joueur.bio || joueur.user?.bio" class="text-sm text-gray-600 mt-4 leading-relaxed max-w-xl">
              {{ joueur.bio || joueur.user?.bio }}
            </p>
          </div>
        </div>
      </div>

      <!-- Experiences -->
      <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-lg font-black text-gray-900">
            <i class="fa-solid fa-timeline text-emerald-600 mr-2"></i>Parcours Professionnel
          </h2>
          <button
            v-if="isOwner"
            @click="showExpForm = true; editingExp = null; resetForm()"
            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold bg-emerald-700 text-white hover:bg-emerald-800 transition"
          >
            <i class="fa-solid fa-plus"></i> Ajouter
          </button>
        </div>

        <!-- Form -->
        <div v-if="showExpForm" class="mb-6 bg-emerald-50 rounded-2xl p-5 border border-emerald-100">
          <h3 class="font-bold text-gray-900 mb-4">
            {{ editingExp ? 'Modifier l\'expérience' : 'Nouvelle expérience' }}
          </h3>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 block">Club *</label>
              <input
                v-model="expForm.club"
                type="text"
                placeholder="FC Barcelona"
                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
              />
            </div>
            <div>
              <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 block">Poste</label>
              <input
                v-model="expForm.position"
                type="text"
                placeholder="Attaquant"
                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
              />
            </div>
            <div>
              <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 block">Date de début</label>
              <input
                v-model="expForm.start_date"
                type="date"
                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
              />
            </div>
            <div>
              <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 block">Date de fin</label>
              <input
                v-model="expForm.end_date"
                type="date"
                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
              />
            </div>
            <div class="col-span-2">
              <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 block">Description</label>
              <textarea
                v-model="expForm.description"
                placeholder="Décrivez votre rôle et vos accomplissements..."
                class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none h-20"
              ></textarea>
            </div>
          </div>
          <div class="flex gap-3 mt-4">
            <button
              @click="resetForm"
              class="flex-1 py-2.5 rounded-xl text-sm font-bold border border-gray-200 text-gray-700 hover:bg-white transition"
            >
              Annuler
            </button>
            <button
              @click="saveExperience"
              :disabled="savingExp || !expForm.club"
              class="flex-1 py-2.5 rounded-xl text-sm font-bold bg-emerald-700 text-white hover:bg-emerald-800 transition disabled:opacity-50"
            >
              <i v-if="savingExp" class="fa-solid fa-spinner animate-spin mr-2"></i>
              {{ editingExp ? 'Sauvegarder' : 'Ajouter' }}
            </button>
          </div>
        </div>

        <!-- Timeline -->
        <div v-if="experiences.length === 0 && !showExpForm" class="text-center py-10">
          <i class="fa-solid fa-timeline text-gray-200 text-5xl mb-3"></i>
          <p class="text-gray-500 font-medium">Aucune expérience enregistrée</p>
        </div>

        <div v-else-if="experiences.length > 0" class="relative">
          <!-- Timeline line -->
          <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-emerald-100"></div>

          <div class="space-y-6">
            <div
              v-for="exp in experiences"
              :key="exp.id"
              class="relative flex gap-5 group"
            >
              <!-- Dot -->
              <div class="w-10 h-10 bg-emerald-600 rounded-2xl flex items-center justify-center shrink-0 z-10 shadow-sm shadow-emerald-900/20">
                <i class="fa-solid fa-futbol text-white text-sm"></i>
              </div>

              <!-- Content -->
              <div class="flex-1 bg-gray-50 rounded-2xl p-5 border border-gray-100 hover:border-emerald-200 transition-colors">
                <div class="flex items-start justify-between">
                  <div>
                    <h4 class="font-black text-gray-900">{{ exp.club }}</h4>
                    <p v-if="exp.position" class="text-xs font-bold text-emerald-700 uppercase tracking-widest mt-0.5">{{ exp.position }}</p>
                    <p class="text-xs text-gray-500 mt-1 font-medium">{{ formatPeriod(exp.start_date, exp.end_date) }}</p>
                  </div>
                  <div v-if="isOwner" class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                    <button
                      @click="startEditExp(exp)"
                      class="p-2 rounded-xl hover:bg-emerald-100 text-emerald-600 transition"
                    >
                      <i class="fa-regular fa-pen-to-square text-sm"></i>
                    </button>
                    <button
                      @click="deleteExperience(exp.id)"
                      :disabled="deletingExpId === exp.id"
                      class="p-2 rounded-xl hover:bg-red-100 text-red-500 transition disabled:opacity-50"
                    >
                      <i class="fa-regular fa-trash-can text-sm"></i>
                    </button>
                  </div>
                </div>
                <p v-if="exp.description" class="text-sm text-gray-600 mt-2 leading-relaxed">{{ exp.description }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Not found -->
    <div v-else class="bg-white rounded-3xl border border-gray-100 p-10 text-center shadow-sm">
      <i class="fa-solid fa-user-slash text-gray-300 text-5xl mb-4"></i>
      <h3 class="font-bold text-gray-800 mb-2">Joueur introuvable</h3>
      <RouterLink to="/" class="text-emerald-700 font-semibold text-sm hover:underline">Retour à l'accueil</RouterLink>
    </div>
  </MainLayout>
</template>
