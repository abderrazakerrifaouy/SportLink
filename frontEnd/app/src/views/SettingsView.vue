<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/AuthStore'
import { useUploadMedia } from '@/services/useUploadMedia'
import { storeToRefs } from 'pinia'
import MainLayout from '@/layouts/MainLayout.vue'
import apiClient from '@/services/api'

const authStore = useAuthStore()
const { user } = storeToRefs(authStore)
const { uploadMedia, isUploading, uploadProgress } = useUploadMedia()

const saving = ref(false)
const success = ref('')
const error = ref('')

const form = ref({
  name: '',
  bio: '',
})

const newProfileImage = ref(null)
const profileImagePreview = ref(null)
const newBannerImage = ref(null)
const bannerImagePreview = ref(null)

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: '',
})
const savingPassword = ref(false)
const passwordError = ref('')
const passwordSuccess = ref('')

const avatarUrl = (name, img) =>
  img || `https://ui-avatars.com/api/?name=${encodeURIComponent(name || 'U')}&bg=064E3B&color=fff&size=200`

onMounted(() => {
  if (user.value) {
    form.value.name = user.value.name || ''
    form.value.bio = user.value.bio || ''
  }
})

const handleProfileImageSelect = (e) => {
  const file = e.target.files[0]
  if (!file) return
  newProfileImage.value = file
  const reader = new FileReader()
  reader.onload = (ev) => { profileImagePreview.value = ev.target.result }
  reader.readAsDataURL(file)
  e.target.value = ''
}

const handleBannerImageSelect = (e) => {
  const file = e.target.files[0]
  if (!file) return
  newBannerImage.value = file
  const reader = new FileReader()
  reader.onload = (ev) => { bannerImagePreview.value = ev.target.result }
  reader.readAsDataURL(file)
  e.target.value = ''
}

const saveProfile = async () => {
  if (!form.value.name.trim()) {
    error.value = 'Le nom est obligatoire.'
    return
  }
  saving.value = true
  error.value = ''
  success.value = ''
  try {
    let profileImageUrl = user.value?.profileImage || null
    let bannerImageUrl = user.value?.bannerImage || null

    if (newProfileImage.value || newBannerImage.value) {
      const filesToUpload = []
      if (newProfileImage.value) filesToUpload.push(newProfileImage.value)
      if (newBannerImage.value) filesToUpload.push(newBannerImage.value)
      const uploaded = await uploadMedia(filesToUpload)
      let idx = 0
      if (newProfileImage.value) { profileImageUrl = uploaded[idx++].url }
      if (newBannerImage.value) { bannerImageUrl = uploaded[idx].url }
    }

    const payload = {
      name: form.value.name.trim(),
      bio: form.value.bio.trim(),
      profileImage: profileImageUrl,
      bannerImage: bannerImageUrl,
    }

    const response = await apiClient.put(`/users/${user.value.id}`, payload)
    authStore.user = response.data
    success.value = 'Profil mis à jour avec succès !'
    newProfileImage.value = null
    newBannerImage.value = null
    profileImagePreview.value = null
    bannerImagePreview.value = null
  } catch (err) {
    error.value = err.response?.data?.message || 'Erreur lors de la sauvegarde.'
  } finally {
    saving.value = false
  }
}

const savePassword = async () => {
  if (!passwordForm.value.current_password || !passwordForm.value.password || !passwordForm.value.password_confirmation) {
    passwordError.value = 'Veuillez remplir tous les champs.'
    return
  }
  if (passwordForm.value.password !== passwordForm.value.password_confirmation) {
    passwordError.value = 'Les mots de passe ne correspondent pas.'
    return
  }
  savingPassword.value = true
  passwordError.value = ''
  passwordSuccess.value = ''
  try {
    await apiClient.put(`/users/${user.value.id}`, {
      current_password: passwordForm.value.current_password,
      password: passwordForm.value.password,
      password_confirmation: passwordForm.value.password_confirmation,
    })
    passwordSuccess.value = 'Mot de passe changé avec succès !'
    passwordForm.value = { current_password: '', password: '', password_confirmation: '' }
  } catch (err) {
    passwordError.value = err.response?.data?.message || 'Erreur lors du changement de mot de passe.'
  } finally {
    savingPassword.value = false
  }
}

const handleLogout = () => {
  authStore.logout()
}
</script>

<template>
  <MainLayout>
    <div class="max-w-2xl mx-auto space-y-6">
      <h1 class="text-2xl font-black text-gray-900">
        <i class="fa-solid fa-gear text-emerald-600 mr-2"></i>Paramètres
      </h1>

      <!-- Profile Settings -->
      <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-lg font-black text-gray-900 mb-6">Informations du profil</h2>

        <!-- Success / Error -->
        <div v-if="success" class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm">
          <i class="fa-solid fa-check-circle mr-2"></i>{{ success }}
        </div>
        <div v-if="error" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
          <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ error }}
        </div>

        <!-- Avatar preview -->
        <div class="flex items-center gap-6 mb-6">
          <div class="relative">
            <img
              :src="profileImagePreview || avatarUrl(user?.name, user?.profileImage)"
              class="h-24 w-24 rounded-3xl object-cover border-2 border-gray-200 shadow-sm"
              alt="Profile"
            />
            <label
              for="profile-img"
              class="absolute bottom-0 right-0 h-8 w-8 bg-emerald-600 hover:bg-emerald-700 rounded-xl flex items-center justify-center cursor-pointer shadow transition"
            >
              <i class="fa-solid fa-camera text-white text-xs"></i>
            </label>
            <input type="file" id="profile-img" hidden accept="image/*" @change="handleProfileImageSelect" />
          </div>
          <div>
            <p class="font-bold text-gray-900">{{ user?.name }}</p>
            <p class="text-xs text-emerald-600 font-bold uppercase tracking-widest">{{ user?.role }}</p>
            <p class="text-xs text-gray-400 mt-1">Cliquez sur la caméra pour changer la photo</p>
          </div>
        </div>

        <!-- Banner preview -->
        <div class="mb-6">
          <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 block">Photo de couverture</label>
          <div class="relative h-32 rounded-2xl overflow-hidden bg-gradient-to-br from-emerald-800 to-emerald-600 cursor-pointer group">
            <img
              v-if="bannerImagePreview || user?.bannerImage"
              :src="bannerImagePreview || user?.bannerImage"
              class="w-full h-full object-cover"
              alt="Banner"
            />
            <label
              for="banner-img"
              class="absolute inset-0 bg-black/0 group-hover:bg-black/30 flex items-center justify-center cursor-pointer transition"
            >
              <div class="opacity-0 group-hover:opacity-100 transition bg-white/20 backdrop-blur rounded-xl px-4 py-2 text-white text-xs font-bold">
                <i class="fa-solid fa-camera mr-2"></i>Changer la couverture
              </div>
            </label>
            <input type="file" id="banner-img" hidden accept="image/*" @change="handleBannerImageSelect" />
          </div>
        </div>

        <!-- Upload progress -->
        <div v-if="isUploading" class="mb-4">
          <div class="flex justify-between text-xs font-bold text-gray-400 mb-1">
            <span>Upload en cours...</span><span>{{ uploadProgress }}%</span>
          </div>
          <div class="w-full bg-gray-100 rounded-full h-1.5">
            <div class="bg-emerald-600 h-1.5 rounded-full transition-all" :style="{ width: uploadProgress + '%' }"></div>
          </div>
        </div>

        <!-- Form -->
        <div class="space-y-4">
          <div>
            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 block">Nom complet *</label>
            <input
              v-model="form.name"
              type="text"
              placeholder="Votre nom"
              class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>
          <div>
            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 block">Bio</label>
            <textarea
              v-model="form.bio"
              placeholder="Parlez-vous de vous..."
              class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none h-28"
            ></textarea>
          </div>
          <button
            @click="saveProfile"
            :disabled="saving || isUploading"
            class="w-full py-3 rounded-xl text-sm font-bold bg-emerald-700 text-white hover:bg-emerald-800 transition disabled:opacity-50"
          >
            <i v-if="saving || isUploading" class="fa-solid fa-spinner animate-spin mr-2"></i>
            {{ saving || isUploading ? 'Sauvegarde...' : 'Sauvegarder le profil' }}
          </button>
        </div>
      </div>

      <!-- Password Change -->
      <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-lg font-black text-gray-900 mb-6">
          <i class="fa-solid fa-lock text-gray-400 mr-2"></i>Changer le mot de passe
        </h2>

        <div v-if="passwordSuccess" class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm">
          <i class="fa-solid fa-check-circle mr-2"></i>{{ passwordSuccess }}
        </div>
        <div v-if="passwordError" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
          <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ passwordError }}
        </div>

        <div class="space-y-4">
          <div>
            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 block">Mot de passe actuel</label>
            <input
              v-model="passwordForm.current_password"
              type="password"
              placeholder="••••••••"
              class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>
          <div>
            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 block">Nouveau mot de passe</label>
            <input
              v-model="passwordForm.password"
              type="password"
              placeholder="••••••••"
              class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>
          <div>
            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 block">Confirmer le nouveau mot de passe</label>
            <input
              v-model="passwordForm.password_confirmation"
              type="password"
              placeholder="••••••••"
              class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
            />
          </div>
          <button
            @click="savePassword"
            :disabled="savingPassword"
            class="w-full py-3 rounded-xl text-sm font-bold bg-gray-800 text-white hover:bg-gray-900 transition disabled:opacity-50"
          >
            <i v-if="savingPassword" class="fa-solid fa-spinner animate-spin mr-2"></i>
            {{ savingPassword ? 'Changement...' : 'Changer le mot de passe' }}
          </button>
        </div>
      </div>

      <!-- Danger Zone -->
      <div class="bg-white rounded-3xl border border-red-100 shadow-sm p-6">
        <h2 class="text-lg font-black text-red-600 mb-4">
          <i class="fa-solid fa-triangle-exclamation mr-2"></i>Zone dangereuse
        </h2>
        <button
          @click="handleLogout"
          class="w-full py-3 rounded-xl text-sm font-bold border-2 border-red-200 text-red-600 hover:bg-red-50 transition"
        >
          <i class="fa-solid fa-right-from-bracket mr-2"></i>Se déconnecter
        </button>
      </div>
    </div>
  </MainLayout>
</template>
