<template>
  <form @submit.prevent="handleNext" class="space-y-6 animate-fadeIn">
    <div class="text-center mb-8">
      <h2 class="text-2xl font-black text-slate-900 tracking-tight">Basic Information</h2>
      <p class="text-slate-500 text-sm mt-1">Let's start with the essentials</p>
    </div>

    <div class="space-y-2">
      <label class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">Full Name</label>
      <div class="relative group">
        <input
          v-model="formData.name"
          type="text"
          placeholder="John Doe"
          @blur="validateName"
          class="bg-slate-50 border border-slate-100 text-slate-900 text-sm rounded-2xl block w-full pl-11 p-3.5 outline-none focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all duration-300"
          :class="{ 'border-red-500 ring-red-500/10': errors.name }"
          required
        />
      </div>
      <p v-if="errors.name" class="text-red-500 text-[10px] font-bold mt-1 ml-1 uppercase">{{ errors.name }}</p>
    </div>

    <div class="space-y-2">
      <label class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">Email Address</label>
      <div class="relative group">
        <input
          v-model="formData.email"
          type="email"
          placeholder="name@example.com"
          @blur="validateEmail"
          class="bg-slate-50 border border-slate-100 text-slate-900 text-sm rounded-2xl block w-full pl-11 p-3.5 outline-none focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all duration-300"
          :class="{ 'border-red-500 ring-red-500/10': errors.email }"
          required
        />
      </div>
      <p v-if="errors.email" class="text-red-500 text-[10px] font-bold mt-1 ml-1 uppercase">{{ errors.email }}</p>
    </div>

    <div class="space-y-3">
      <label class="text-xs font-bold uppercase tracking-widest text-slate-400 ml-1 block">I am a</label>
      <div class="grid grid-cols-2 gap-4">
        <div
          @click="formData.role = 'JOUEUR'; validateRole()"
          class="relative p-4 rounded-2xl border-2 cursor-pointer transition-all duration-300 flex flex-col items-center text-center space-y-2"
          :class="formData.role === 'JOUEUR' ? 'border-blue-600 bg-blue-50 ring-4 ring-blue-500/10' : 'border-slate-100 bg-slate-50 hover:border-slate-200'"
        >
          <span class="text-xs font-black text-slate-900 uppercase">Joueur</span>
          <div v-if="formData.role === 'JOUEUR'" class="absolute top-2 right-2">
            <div class="bg-blue-600 text-white rounded-full p-0.5">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg>
            </div>
          </div>
        </div>

        <div
          @click="formData.role = 'CLUB_ADMIN'; validateRole()"
          class="relative p-4 rounded-2xl border-2 cursor-pointer transition-all duration-300 flex flex-col items-center text-center space-y-2"
          :class="formData.role === 'CLUB_ADMIN' ? 'border-blue-600 bg-blue-50 ring-4 ring-blue-500/10' : 'border-slate-100 bg-slate-50 hover:border-slate-200'"
        >
          <span class="text-xs font-black text-slate-900 uppercase">Club Admin</span>
          <div v-if="formData.role === 'CLUB_ADMIN'" class="absolute top-2 right-2">
            <div class="bg-blue-600 text-white rounded-full p-0.5">
              <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg>
            </div>
          </div>
        </div>
      </div>
      <p v-if="errors.role" class="text-red-500 text-[10px] font-bold mt-1 ml-1 uppercase">{{ errors.role }}</p>
    </div>

    <div class="flex gap-4 mt-10 pt-4">
      <button
        type="button"
        disabled
        class="flex-1 py-4 bg-slate-100 text-slate-400 font-bold text-xs uppercase tracking-widest rounded-2xl cursor-not-allowed opacity-50"
      >
        Previous
      </button>

      <button
        type="submit"
        class="flex-1 py-4 bg-slate-900 hover:bg-blue-600 text-white font-bold text-xs uppercase tracking-widest rounded-2xl shadow-lg shadow-blue-500/10 transition-all duration-300 active:scale-95"
      >
        Next Step
      </button>
    </div>
  </form>
</template>

<script setup>
import { reactive } from 'vue'
import { useAuthStore } from '@/stores/AuthStore'

const emit = defineEmits(['next'])
const authStore = useAuthStore()

const formData = authStore.formData

const errors = reactive({
  name: '',
  email: '',
  role: '',
})

const validateName = () => {
  errors.name = ''
  if (!formData.name || formData.name.trim().length < 3) {
    errors.name = 'Minimum 3 characters required'
  }
}

const validateEmail = () => {
  errors.email = ''
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!formData.email || !regex.test(formData.email)) {
    errors.email = 'Please enter a valid email'
  }
}

const validateRole = () => {
  errors.role = ''
  if (!formData.role) {
    errors.role = 'Please select your role'
  }
}

const validateForm = () => {
  validateName()
  validateEmail()
  validateRole()
  return !errors.name && !errors.email && !errors.role
}

const handleNext = () => {
  if (!validateForm()) return
  emit('next')
}
</script>
