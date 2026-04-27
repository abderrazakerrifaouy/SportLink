<template>
  <div class="animate-fadeIn">
    <form @submit.prevent="handleLogin" class="space-y-6">

      <div class="space-y-2">
        <label for="email" class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">
          Adresse e-mail
        </label>
        <div class="relative group">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
            </svg>
          </div>
          <input
            id="email"
            v-model="credentials.email"
            type="email"
            placeholder="name@example.com"
            class="w-full bg-slate-50 border border-slate-100 text-slate-900 text-sm rounded-2xl block pl-11 p-3.5 outline-none focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all duration-300"
            :class="{ 'border-red-500 ring-red-500/10': errors.email }"
            required
          />
        </div>
        <p v-if="errors.email" class="text-red-500 text-[10px] font-bold mt-1 ml-1 uppercase">{{ errors.email }}</p>
      </div>

      <div class="space-y-2">
        <div class="flex justify-between items-center ml-1">
          <label for="password" class="text-xs font-bold uppercase tracking-widest text-slate-400">
            Mot de passe
          </label>
          <a href="#" @click.prevent="handleForgotPassword" class="text-[11px] font-bold text-blue-600 hover:text-indigo-600 transition-colors">
            Mot de passe oublie ?
          </a>
        </div>
        <div class="relative group">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
          <input
            id="password"
            v-model="credentials.password"
            type="password"
            placeholder="••••••••"
            class="w-full bg-slate-50 border border-slate-100 text-slate-900 text-sm rounded-2xl block pl-11 p-3.5 outline-none focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all duration-300"
            :class="{ 'border-red-500 ring-red-500/10': errors.password }"
            required
          />
        </div>
        <p v-if="errors.password" class="text-red-500 text-[10px] font-bold mt-1 ml-1 uppercase">{{ errors.password }}</p>
      </div>

      <div class="flex items-center px-1">
        <label class="relative flex items-center cursor-pointer">
          <input
            v-model="credentials.remember"
            type="checkbox"
            class="sr-only peer"
          />
          <div class="w-5 h-5 bg-slate-100 border border-slate-200 rounded-md peer peer-checked:bg-blue-600 peer-checked:border-blue-600 transition-all">
             <svg class="w-3.5 h-3.5 text-white absolute top-1 left-0.5 opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
             </svg>
          </div>
          <span class="ml-3 text-sm font-medium text-slate-500 select-none">Se souvenir de cet appareil</span>
        </label>
      </div>

      <transition name="shake">
        <div v-if="errors.general" class="flex items-center p-4 bg-red-50 text-red-700 rounded-2xl border border-red-100">
          <svg class="w-5 h-5 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          <span class="text-xs font-bold">{{ errors.general }}</span>
        </div>
      </transition>

      <button
        :disabled="isLoading"
        class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-black rounded-2xl text-white bg-slate-900 hover:bg-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-500/20 transition-all duration-300 active:scale-[0.98] disabled:opacity-70"
      >
        <span v-if="isLoading" class="absolute left-0 inset-y-0 flex items-center pl-3">
          <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </span>
        {{ isLoading ? 'VERIFICATION...' : 'SE CONNECTER' }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useAuthStore } from '@/stores/AuthStore'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const credentials = reactive({
  email: '',
  password: '',
  remember: false,
})

const errors = reactive({
  email: '',
  password: '',
  general: '',
})

const isLoading = ref(false)

const validateForm = () => {
  errors.email = ''
  errors.password = ''
  let isValid = true

  if (!credentials.email) {
    errors.email = 'Adresse e-mail obligatoire'
    isValid = false
  } else if (!/\S+@\S+\.\S+/.test(credentials.email)) {
    errors.email = 'Entrez une adresse e-mail valide'
    isValid = false
  }

  if (!credentials.password) {
    errors.password = 'Mot de passe obligatoire'
    isValid = false
  }

  return isValid
}

const handleLogin = async () => {
  if (!validateForm()) return

  isLoading.value = true
  errors.general = ''

  try {
    await authStore.login(credentials)
    router.push('/dashboard')
  } catch (error) {
    errors.general = error.response?.data?.message || 'Identifiants invalides'
  } finally {
    isLoading.value = false
  }
}

const handleForgotPassword = () => {
  router.push('/auth/forgot-password')
}
</script>

<style scoped>
.animate-fadeIn {
  animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.98); }
  to { opacity: 1; transform: scale(1); }
}

/* Shake animation for errors */
.shake-enter-active {
  animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both;
}

@keyframes shake {
  10%, 90% { transform: translate3d(-1px, 0, 0); }
  20%, 80% { transform: translate3d(2px, 0, 0); }
  30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
  40%, 60% { transform: translate3d(4px, 0, 0); }
}
</style>
