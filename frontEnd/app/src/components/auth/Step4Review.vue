<template>
  <div class="space-y-6 animate-fadeIn">
    <div class="mb-6 text-left">
      <h2 class="text-xl font-bold text-slate-900">Vérification finale</h2>
      <p class="text-slate-500 text-sm">Vérifiez vos informations avant de valider.</p>
    </div>

    <div class="space-y-4">
      <div class="bg-slate-50/50 rounded-2xl border border-slate-100 p-5 space-y-4">
        <div class="grid grid-cols-1 gap-3">
          <div class="flex justify-between items-center border-b border-slate-100 pb-2">
            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Identité</span>
            <span class="text-sm font-semibold text-slate-700">{{ authStore.formData.name }}</span>
          </div>
          <div class="flex justify-between items-center border-b border-slate-100 pb-2">
            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Email</span>
            <span class="text-sm font-semibold text-slate-700">{{ authStore.formData.email }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Rôle</span>
            <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded-md font-bold uppercase tracking-tighter">
              {{ authStore.formData.role }}
            </span>
          </div>
        </div>

        <div class="pt-2 flex items-center space-x-4">
          <div class="relative">
             <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Profil</p>
             <div class="w-14 h-14 rounded-full border-2 border-white shadow-sm overflow-hidden bg-slate-200">
               <img v-if="authStore.formData.profileImage" :src="getImagePreview('profile')" class="w-full h-full object-cover" />
               <div v-else class="w-full h-full flex items-center justify-center text-slate-400 text-[8px]">N/A</div>
             </div>
          </div>
          <div class="flex-1">
             <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Bannière</p>
             <div class="w-full h-14 rounded-xl border-2 border-white shadow-sm overflow-hidden bg-slate-200">
               <img v-if="authStore.formData.bannerImage" :src="getImagePreview('banner')" class="w-full h-full object-cover" />
               <div v-else class="w-full h-full flex items-center justify-center text-slate-400 text-[8px]">N/A</div>
             </div>
          </div>
        </div>
      </div>

      <div class="p-4 bg-slate-50/50 rounded-2xl border border-slate-100">
        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Bio</p>
        <p class="text-xs text-slate-600 leading-relaxed italic">
          "{{ authStore.formData.bio || 'Aucune description fournie.' }}"
        </p>
      </div>

      <div class="px-2">
        <label class="relative flex items-start cursor-pointer group">
          <div class="flex items-center h-5">
            <input v-model="agreed" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" />
          </div>
          <div class="ml-3 text-[11px] leading-tight text-slate-500 font-medium select-none">
            J'accepte les <a href="#" class="text-blue-600 font-bold hover:underline">Conditions d'utilisation</a>
            et la <a href="#" class="text-blue-600 font-bold hover:underline">Politique de confidentialité</a>.
          </div>
        </label>
      </div>
    </div>

    <div v-if="isUploading || isLoading" class="space-y-2 py-2">
      <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-blue-600">
        <span>{{ isUploading ? 'Téléchargement des médias' : 'Création du compte' }}</span>
        <span v-if="isUploading">{{ uploadProgress }}%</span>
      </div>
      <div class="w-full bg-slate-100 rounded-full h-1">
        <div class="bg-blue-600 h-1 rounded-full transition-all duration-500" :style="{ width: isUploading ? uploadProgress + '%' : '90%' }"></div>
      </div>
    </div>

    <div v-if="errors.general || uploadError" class="p-3 bg-red-50 text-red-600 rounded-xl border border-red-100 text-[11px] font-bold flex items-center">
       <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
       {{ errors.general || uploadError }}
    </div>

    <div class="flex gap-3 mt-10">
      <button
        type="button"
        :disabled="isLoading || isUploading"
        @click="$emit('previous')"
        class="flex-1 py-3 text-slate-500 text-xs font-bold uppercase tracking-widest hover:text-slate-800 transition-colors disabled:opacity-30"
      >
        Retour
      </button>

      <button
        type="button"
        :disabled="!agreed || isLoading || isUploading"
        @click="handleRegister"
        class="flex-[2] py-3 bg-slate-900 hover:bg-blue-600 text-white font-bold text-xs uppercase tracking-widest rounded-xl transition-all active:scale-[0.98] shadow-sm disabled:bg-slate-200 disabled:text-slate-400"
      >
        <span v-if="isUploading || isLoading">Traitement...</span>
        <span v-else>Créer mon compte</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useAuthStore } from '@/stores/AuthStore'
import { useUploadMedia } from '@/services/useUploadMedia'
import { useRouter } from 'vue-router'

const emit = defineEmits(['previous'])
const authStore = useAuthStore()
const router = useRouter()

const { uploadMedia, isUploading, uploadProgress, uploadError } = useUploadMedia()

const agreed = ref(false)
const isLoading = ref(false)
const errors = reactive({ general: '' })

const getImagePreview = (type) => {
  const file = type === 'profile' ? authStore.formData.profileImage : authStore.formData.bannerImage
  if (file instanceof File) return URL.createObjectURL(file)
  return file
}

const handleRegister = async () => {
  if (!agreed.value) {
    errors.general = 'Veuillez accepter les conditions.'
    return
  }
  errors.general = ''

  try {
    const filesToUpload = []
    const fieldMapping = []

    if (authStore.formData.profileImage instanceof File) {
      filesToUpload.push(authStore.formData.profileImage)
      fieldMapping.push('profile_image')
    }
    if (authStore.formData.bannerImage instanceof File) {
      filesToUpload.push(authStore.formData.bannerImage)
      fieldMapping.push('banner_image')
    }

    if (filesToUpload.length > 0) {
      const uploadResults = await uploadMedia(filesToUpload)
      uploadResults.forEach((res, index) => {
        authStore.formData[fieldMapping[index]] = res.url
      })
    }

    isLoading.value = true
    await authStore.register()
    router.push('/')
  } catch (error) {
    errors.general = error.response?.data?.message || 'Erreur lors de l\'inscription.'
  } finally {
    isLoading.value = false
  }
}
</script>
