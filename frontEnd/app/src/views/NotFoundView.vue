<template>
  <div class="min-h-screen bg-[#F3F4F6] flex items-center justify-center p-6">
    <div class="text-center max-w-md">
      <div class="text-9xl font-black text-emerald-700 leading-none mb-2">404</div>
      <div class="w-32 h-1 bg-emerald-600 mx-auto rounded-full mb-6"></div>
      <h1 class="text-2xl font-black text-gray-900 mb-3">Page introuvable</h1>
      <p class="text-gray-500 mb-8 leading-relaxed">
        La page que vous recherchez n'existe pas ou a été déplacée.
      </p>
      <div class="flex gap-3 justify-center">
        <button
          @click="$router.back()"
          class="flex items-center gap-2 px-6 py-3 rounded-2xl border border-gray-200 text-gray-700 font-bold text-sm hover:bg-white transition"
        >
          <i class="fa-solid fa-arrow-left"></i> Retour
        </button>
        <RouterLink
          to="/"
          class="flex items-center gap-2 px-6 py-3 rounded-2xl bg-emerald-700 text-white font-bold text-sm hover:bg-emerald-800 transition no-underline"
        >
          <i class="fa-solid fa-home"></i> Accueil
        </RouterLink>
      </div>
    </div>
  </div>
</template>
