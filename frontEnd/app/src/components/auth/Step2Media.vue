<template>
  <form @submit.prevent="handleNext" class="space-y-8 animate-fadeIn">
    <div class="mb-6">
      <h2 class="text-xl font-bold text-slate-900">Médias du profil</h2>
      <p class="text-slate-500 text-sm">Personnalisez votre apparence sur la plateforme.</p>
    </div>

    <div class="flex flex-col items-center space-y-4">
      <label class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 self-start ml-1">Photo de profil</label>

      <div class="relative group">
        <div
          @click="$refs.profileInput.click()"
          class="w-28 h-28 rounded-full border-2 border-dashed border-slate-200 flex flex-col items-center justify-center cursor-pointer hover:border-blue-500 hover:bg-slate-50 transition-all overflow-hidden bg-slate-50/50"
        >
          <template v-if="!profileImagePreview">
            <svg class="w-6 h-6 text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span class="text-[10px] font-bold text-slate-400 uppercase">Ajouter</span>
          </template>

          <img v-else :src="profileImagePreview" class="w-full h-full object-cover animate-fadeIn" />
        </div>

        <button
          v-if="profileImagePreview"
          type="button"
          @click="removeProfileImage"
          class="absolute -top-1 -right-1 bg-white shadow-md border border-slate-100 text-slate-400 hover:text-red-500 w-7 h-7 rounded-full flex items-center justify-center transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2"/></svg>
        </button>
      </div>
      <input ref="profileInput" type="file" accept="image/*" hidden @change="handleProfileImageChange" />
    </div>

    <div class="space-y-3">
      <label class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 ml-1">Image de couverture</label>

      <div
        @click="$refs.bannerInput.click()"
        class="relative h-32 rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center cursor-pointer hover:border-blue-500 hover:bg-slate-50 transition-all overflow-hidden bg-slate-50/50"
      >
        <template v-if="!bannerImagePreview">
          <svg class="w-6 h-6 text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <span class="text-[10px] font-bold text-slate-400 uppercase">Choisir une bannière</span>
        </template>

        <img v-else :src="bannerImagePreview" class="w-full h-full object-cover animate-fadeIn" />

        <div v-if="bannerImagePreview" class="absolute inset-0 bg-black/20 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center">
          <button type="button" @click.stop="removeBannerImage" class="bg-white/90 p-2 rounded-lg text-red-600 font-bold text-xs uppercase tracking-tighter">Supprimer</button>
        </div>
      </div>
      <input ref="bannerInput" type="file" accept="image/*" hidden @change="handleBannerImageChange" />
    </div>

    <div class="flex gap-3 mt-10">
      <button
        type="button"
        @click="$emit('previous')"
        class="flex-1 py-3 text-slate-500 text-xs font-bold uppercase tracking-widest hover:text-slate-800 transition-colors"
      >
        Retour
      </button>

      <button
        type="submit"
        class="flex-1 py-3 bg-slate-900 hover:bg-blue-600 text-white font-bold text-xs uppercase tracking-widest rounded-xl transition-all active:scale-[0.98]"
      >
        Suivant
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/AuthStore'

const emit = defineEmits(['next', 'previous'])
const authStore = useAuthStore()

const profileImagePreview = ref(null)
const bannerImagePreview = ref(null)
const profileInput = ref(null)
const bannerInput = ref(null)

const handleProfileImageChange = (event) => {
  const file = event.target.files?.[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => profileImagePreview.value = e.target.result
    reader.readAsDataURL(file)
    authStore.setProfileImage(file)
  }
}

const handleBannerImageChange = (event) => {
  const file = event.target.files?.[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => bannerImagePreview.value = e.target.result
    reader.readAsDataURL(file)
    authStore.setBannerImage(file)
  }
}

const removeProfileImage = () => {
  profileImagePreview.value = null
  profileInput.value.value = ''
}

const removeBannerImage = () => {
  bannerImagePreview.value = null
  bannerInput.value.value = ''
}

const handleNext = () => emit('next')
</script>

<style scoped>
.animate-fadeIn {
  animation: fadeIn 0.4s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
</style>
