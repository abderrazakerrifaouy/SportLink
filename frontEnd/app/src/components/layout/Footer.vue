<template>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-12 lg:gap-8">

    <div class="col-span-1 md:col-span-1">
      <router-link to="/" class="group">
        <h2 class="text-2xl font-black italic tracking-tighter transition-transform group-hover:scale-105">
          <span class="text-blue-600">SPORT</span><span class="text-slate-900">LINK</span>
        </h2>
      </router-link>
      <p class="mt-4 text-slate-500 text-sm leading-relaxed max-w-xs">
        La plateforme ultime pour connecter les joueurs, les clubs et les passionnés de sport au Maroc.
      </p>
      <div class="flex space-x-4 mt-6">
        <a href="#" class="text-slate-400 hover:text-blue-600 transition-colors">
          <span class="sr-only">Instagram</span>
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4s1.791-4 4-4 4 1.791 4 4-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
        </a>
        <a href="#" class="text-slate-400 hover:text-blue-600 transition-colors">
          <span class="sr-only">LinkedIn</span>
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
        </a>
      </div>
    </div>

    <div>
      <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-slate-900 mb-6">Plateforme</h3>
      <ul class="space-y-4">
        <li><router-link to="/explore" class="text-slate-500 hover:text-blue-600 text-sm transition-colors">Découvrir les clubs</router-link></li>
        <li><router-link to="/players" class="text-slate-500 hover:text-blue-600 text-sm transition-colors">Chercher des joueurs</router-link></li>
        <li><router-link to="/events" class="text-slate-500 hover:text-blue-600 text-sm transition-colors">Événements</router-link></li>
      </ul>
    </div>

    <div>
      <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-slate-900 mb-6">Support</h3>
      <ul class="space-y-4">
        <li><router-link to="/help" class="text-slate-500 hover:text-blue-600 text-sm transition-colors">Centre d'aide</router-link></li>
        <li><router-link to="/contact" class="text-slate-500 hover:text-blue-600 text-sm transition-colors">Contactez-nous</router-link></li>
        <li><router-link to="/terms" class="text-slate-500 hover:text-blue-600 text-sm transition-colors">Conditions d'utilisation</router-link></li>
      </ul>
    </div>

    <div>
      <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-slate-900 mb-6">Newsletter</h3>
      <p class="text-slate-500 text-sm mb-4">Restez informé sur les nouveaux clubs.</p>
      <form class="flex flex-col space-y-2">
        <input
          type="email"
          placeholder="votre@email.com"
          class="bg-slate-50 border border-slate-200 text-sm rounded-xl px-4 py-2.5 outline-none focus:border-blue-600 transition-all"
        />
        <button class="bg-slate-900 text-white text-xs font-bold uppercase tracking-widest py-2.5 rounded-xl hover:bg-blue-600 transition-all">
          S'abonner
        </button>
      </form>
    </div>

    <div class="col-span-1 md:col-span-4 pt-12 mt-12 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
      <p class="text-slate-400 text-xs font-medium">
        © 2026 SportLink. Fabriqué avec  au Maroc.
      </p>
      <div class="flex items-center space-x-6 text-[10px] font-bold uppercase tracking-widest text-slate-400">
        <span class="flex items-center">
          <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
          System Status: Online
        </span>
      </div>
    </div>
  </div>
</template>
