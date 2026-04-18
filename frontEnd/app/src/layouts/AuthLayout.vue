<template>
  <div class="min-h-screen bg-white text-slate-900 font-sans">
    <div class="mx-auto grid min-h-screen grid-cols-1 lg:grid-cols-2">

      <aside class="relative hidden overflow-hidden lg:block">
        <div class="absolute inset-0 bg-[url('/src/assets/bg.png')] bg-cover bg-center" />

        <div class="absolute inset-0 bg-gradient-to-b from-[#06200D]/80 via-[#0B2E16]/85 to-[#06200D]/95" />

        <div class="relative z-10 flex items-center gap-2 p-12">
          <div class="flex items-center gap-2">
            <div class="h-8 w-8 bg-emerald-500 rounded-md flex items-center justify-center">
              <span class="text-white text-xs">★</span>
            </div>
            <span class="text-2xl font-bold tracking-tight text-white uppercase">SportLink</span>
          </div>
        </div>

        <div class="relative z-10 flex h-[calc(100%-160px)] flex-col justify-center px-16">
          <h1 class="text-[72px] font-bold leading-[0.95] tracking-tighter text-white uppercase">
            Dominez le <br />
            <span class="text-emerald-400 font-light italic">Terrain.</span>
          </h1>

          <p class="mt-8 max-w-sm text-lg leading-relaxed text-white/70">
            La plateforme d'élite pour les athlètes de haut niveau et les clubs visionnaires. Connectez-vous à l'excellence.
          </p>

          <div class="mt-24 flex gap-16">
            <div>
              <div class="text-4xl font-bold text-white tracking-tighter">500+</div>
              <div class="mt-1 text-[10px] font-black uppercase tracking-[0.2em] text-white/40">Clubs Pro</div>
            </div>
            <div>
              <div class="text-4xl font-bold text-white tracking-tighter">12k</div>
              <div class="mt-1 text-[10px] font-black uppercase tracking-[0.2em] text-white/40">Athlètes</div>
            </div>
          </div>
        </div>

        <div class="absolute bottom-0 left-[20%] h-32 w-px bg-gradient-to-t from-white/30 to-transparent" />
      </aside>

      <main class="flex flex-col bg-white">
        <div class="px-8 pt-10 lg:px-20">
          <div class="flex items-center justify-between border-b border-slate-100">
            <div class="flex gap-8">
              <button
                @click="$emit('navigate', 'login')"
                class="pb-4 text-[11px] font-bold tracking-widest transition-all"
                :class="activeTab === 'login' ? 'border-b-2 border-emerald-900 text-emerald-900' : 'text-slate-400 hover:text-slate-600'"
              >
                CONNEXION
              </button>
              <button
                @click="$emit('navigate', 'register')"
                class="pb-4 text-[11px] font-bold tracking-widest transition-all"
                :class="activeTab === 'register' ? 'border-b-2 border-emerald-900 text-emerald-900' : 'text-slate-400 hover:text-slate-600'"
              >
                INSCRIPTION
              </button>
            </div>
          </div>
        </div>

        <div class="flex flex-1 items-center justify-center px-8 py-12 lg:px-20">
          <div class="w-full max-w-md">
            <slot />
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
defineProps({ activeTab: String });
defineEmits(['navigate']);
</script>
