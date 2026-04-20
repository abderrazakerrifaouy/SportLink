<template>
  <form @submit.prevent="handleNext" class="space-y-6 animate-fadeIn">
    <div class="mb-6">
      <h2 class="text-xl font-bold text-slate-900">Sécurité & Bio</h2>
      <p class="text-slate-500 text-sm">Protégez votre compte et dites-en plus sur vous.</p>
    </div>

    <div class="space-y-1.5">
      <label class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 ml-1">Mot de passe</label>
      <input
        v-model="formData.password"
        type="password"
        placeholder="••••••••"
        @blur="validatePassword"
        class="w-full bg-white border border-slate-200 text-slate-900 text-sm rounded-xl block p-3 outline-none focus:border-blue-600 transition-all duration-200"
        :class="{ 'border-red-500 bg-red-50/30': errors.password }"
        required
      />
      <p v-if="errors.password" class="text-red-500 text-[10px] font-medium mt-1 ml-1">{{ errors.password }}</p>
    </div>

    <div class="space-y-1.5">
      <label class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 ml-1">Confirmer le mot de passe</label>
      <input
        v-model="formData.password_confirmation"
        type="password"
        placeholder="••••••••"
        @blur="validatePasswordConfirmation"
        class="w-full bg-white border border-slate-200 text-slate-900 text-sm rounded-xl block p-3 outline-none focus:border-blue-600 transition-all duration-200"
        :class="{ 'border-red-500 bg-red-50/30': errors.passwordConfirmation }"
        required
      />
      <p v-if="errors.passwordConfirmation" class="text-red-500 text-[10px] font-medium mt-1 ml-1">{{ errors.passwordConfirmation }}</p>
    </div>

    <div class="space-y-1.5">
      <div class="flex justify-between items-end ml-1">
        <label class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Bio (Optionnel)</label>
        <span class="text-[10px] font-bold" :class="formData.bio?.length > 150 ? 'text-red-500' : 'text-slate-400'">
          {{ formData.bio?.length || 0 }}/150
        </span>
      </div>
      <textarea
        v-model="formData.bio"
        rows="3"
        maxlength="155"
        placeholder="Parlez-nous de votre parcours sportif..."
        @blur="validateBio"
        class="w-full bg-white border border-slate-200 text-slate-900 text-sm rounded-xl block p-3 outline-none focus:border-blue-600 transition-all duration-200 resize-none"
        :class="{ 'border-red-500 bg-red-50/30': errors.bio }"
      ></textarea>
      <p v-if="errors.bio" class="text-red-500 text-[10px] font-medium mt-1 ml-1">{{ errors.bio }}</p>
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
        class="flex-1 py-3 bg-slate-900 hover:bg-blue-600 text-white font-bold text-xs uppercase tracking-widest rounded-xl transition-all active:scale-[0.98] shadow-sm"
      >
        Suivant
      </button>
    </div>
  </form>
</template>

<script setup>
import { reactive } from 'vue'
import { useAuthStore } from '@/stores/AuthStore'

const emit = defineEmits(['next', 'previous'])
const authStore = useAuthStore()
const formData = authStore.formData

const errors = reactive({
  password: '',
  passwordConfirmation: '',
  bio: '',
})

const validatePassword = () => {
  errors.password = (formData.password && formData.password.length >= 6)
    ? ''
    : 'Le mot de passe doit contenir au moins 6 caractères'
}

const validatePasswordConfirmation = () => {
  errors.passwordConfirmation = (formData.password === formData.password_confirmation)
    ? ''
    : 'Les mots de passe ne correspondent pas'
}

const validateBio = () => {
  errors.bio = (formData.bio && formData.bio.length > 150)
    ? 'La bio est trop longue'
    : ''
}

const handleNext = () => {
  validatePassword()
  validatePasswordConfirmation()
  validateBio()

  if (!errors.password && !errors.passwordConfirmation && !errors.bio) {
    emit('next')
  }
}
</script>
