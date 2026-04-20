<template>
  <div class="min-h-screen flex items-center justify-center bg-[#0f172a] relative overflow-hidden px-4">

    <div class="absolute top-[-10%] left-[-10%] w-72 h-72 bg-blue-600/20 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-indigo-600/20 rounded-full blur-[150px]"></div>

    <div class="w-full max-w-md z-10">
      <div class="text-center mb-8 animate-fadeIn">
        <router-link to="/" class="inline-block">
          <h1 class="text-5xl font-black tracking-tighter italic">
            <span class="bg-linear-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent">SPORT</span>
            <span class="text-white ml-1">LINK</span>
          </h1>
        </router-link>
      </div>

      <div class="bg-white/95 backdrop-blur-xl rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.3)] p-8 md:p-12 animate-slideUp relative">

        <div class="mb-8 text-left">
          <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">
            {{ isLogin ? 'Marhba bik !' : 'Ouedd l-kont dyalk' }}
          </h2>
          <p class="text-slate-500 font-medium mt-2">
            {{ isLogin ? 'Connecter bach t-suivi les joueurs o les clubs.' : 'Join 2000+ players and clubs in the community.' }}
          </p>
        </div>

        <div class="min-h-[300px]">
          <router-view />
        </div>

        <div class="relative my-8">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-slate-100"></div>
          </div>
          <div class="relative flex justify-center text-xs uppercase">
            <span class="bg-white px-4 text-slate-400 font-bold tracking-widest">OULA</span>
          </div>
        </div>

        <div class="text-center">
          <p v-if="isLogin" class="text-slate-600 font-medium">
            Ma 3ndekch kont?
            <router-link to="/auth/register" class="text-blue-600 font-bold hover:text-indigo-600 transition-all underline underline-offset-4 decoration-2">
              T-tsajel hna
            </router-link>
          </p>
          <p v-else class="text-slate-600 font-medium">
            3ndek deja kont?
            <router-link to="/auth/login" class="text-blue-600 font-bold hover:text-indigo-600 transition-all underline underline-offset-4 decoration-2">
              Dkhol men hna
            </router-link>
          </p>
        </div>
      </div>

      <div class="mt-8 flex justify-center items-center space-x-2 text-white/40 text-xs uppercase tracking-[0.2em] font-bold">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 4.925-3.067 9.13-7.4 10.803a.75.75 0 01-.6 0C5.633 16.13 2.567 11.925 2.567 7c0-.68.056-1.35.166-2.001zm6.584 10.83a10.46 10.46 0 005.178-3.434.75.75 0 111.144.966 11.961 11.961 0 01-6.322 4.137.75.75 0 01-.3-.027 11.961 11.961 0 01-6.347-4.11.75.75 0 111.144-.966 10.46 10.46 0 005.178 3.434z" clip-rule="evenodd" />
        </svg>
        <span>100% Secure Platform</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const isLogin = computed(() => route.path.includes('login'))
</script>

<style scoped>
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(40px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.animate-slideUp {
  animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.animate-fadeIn {
  animation: fadeIn 1s ease-out;
}
</style>
