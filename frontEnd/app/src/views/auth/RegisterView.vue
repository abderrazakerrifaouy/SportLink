<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/AuthStore'
import AuthLayout from '@/layouts/AuthLayout.vue'

const authStore = useAuthStore()

// Fixed: Hayyedna <'player' | 'club'> bach tkhdem f JS 3adi
const profile = ref('JOUEUR')
const fullName = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const accepted = ref(false)
const showPassword = ref(false)

const onSubmit = async () => {
  if (!fullName.value || !email.value || !password.value || !confirmPassword.value) {
    alert("Veuillez remplir tous les champs.")
    return
  }

  if (password.value !== confirmPassword.value) {
    alert("Les mots de passe ne correspondent pas.")
    return
  }

  try {
    // Siftna object wahed l-Store
    await authStore.register({
       name: fullName.value,
       email: email.value,
       password: password.value,
       password_confirmation: confirmPassword.value,
       role: profile.value // Ghadi ikun 'player' wala 'club'
    })
  } catch (error) {
    alert("Une erreur est survenue lors de l'inscription.")
    console.error('Registration error:', error)
  }
}
</script>

<template>
  <AuthLayout
    activeTab="register"
    @navigate="(to) => $router.push(to === 'login' ? '/login' : '/register')"
  >
    <h2 class="text-2xl font-semibold text-slate-900">Créer un compte</h2>
    <p class="mt-2 text-sm text-slate-500">Prêt à propulser votre carrière sportive ?</p>

    <form class="mt-8 space-y-5" @submit.prevent="onSubmit">
      <div>
        <p class="text-[11px] font-semibold tracking-wide text-slate-500 uppercase">Type de profil</p>
        <div class="mt-3 grid grid-cols-2 gap-3">
          <button
            type="button"
            class="rounded-md border px-4 py-4 text-left text-xs font-semibold transition"
            :class="profile === 'JOUEUR'
              ? 'border-emerald-700 bg-emerald-50 text-emerald-900 ring-2 ring-emerald-600/10'
              : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
            @click="profile = 'JOUEUR'"
          >
            <div>JOUEUR</div>
            <div class="mt-0.5 text-[11px] font-medium text-slate-500">Athlète</div>
          </button>

          <button
            type="button"
            class="rounded-md border px-4 py-4 text-left text-xs font-semibold transition"
            :class="profile === 'CLUB_ADMIN'
              ? 'border-emerald-700 bg-emerald-50 text-emerald-900 ring-2 ring-emerald-600/10'
              : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
            @click="profile = 'CLUB_ADMIN'"
          >
            <div>CLUB</div>
            <div class="mt-0.5 text-[11px] font-medium text-slate-500">Organisation</div>
          </button>
        </div>
      </div>

      <div>
        <label class="text-[11px] font-semibold tracking-wide text-slate-500">NOM COMPLET</label>
        <div class="mt-2">
          <input v-model="fullName" type="text" placeholder="Jean Dupont" class="w-full rounded-md border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-600/15 outline-none" />
        </div>
      </div>

      <div>
        <label class="text-[11px] font-semibold tracking-wide text-slate-500">ADRESSE EMAIL</label>
        <div class="mt-2">
          <input v-model="email" type="email" placeholder="nom@exemple.com" class="w-full rounded-md border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-600/15 outline-none" />
        </div>
      </div>

      <div>
        <label class="text-[11px] font-semibold tracking-wide text-slate-500">MOT DE PASSE</label>
        <div class="mt-2 relative">
          <input :type="showPassword ? 'text' : 'password'" v-model="password" placeholder="••••••••" class="w-full rounded-md border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-emerald-600 outline-none" />
          <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-4 text-[10px] font-bold text-slate-400 hover:text-emerald-700">
            {{ showPassword ? 'HIDE' : 'SHOW' }}
          </button>
        </div>
      </div>

      <div>
        <label class="text-[11px] font-semibold tracking-wide text-slate-500">CONFIRMER LE MOT DE PASSE</label>
        <div class="mt-2">
          <input v-model="confirmPassword" type="password" placeholder="••••••••" class="w-full rounded-md border border-slate-200 bg-slate-50 px-4 py-3 text-sm focus:border-emerald-600 outline-none" />
        </div>
      </div>

      <label class="flex items-start gap-3 text-xs text-slate-600 cursor-pointer">
        <input v-model="accepted" type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-600/20" />
        <span>J'accepte les <a class="font-semibold text-emerald-800 hover:underline" href="#">conditions</a>.</span>
      </label>

      <button
        type="submit"
        :disabled="!accepted"
        class="w-full rounded-md bg-emerald-800 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-900 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        CRÉER MON COMPTE →
      </button>

      <p class="pt-2 text-center text-xs text-slate-500">
        Déjà un compte ?
        <button type="button" class="font-semibold text-emerald-800 hover:underline" @click="$router.push('/login')">Connectez-vous</button>
      </p>
    </form>
  </AuthLayout>
</template>
