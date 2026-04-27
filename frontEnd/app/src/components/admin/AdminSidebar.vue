<template>
  <aside class="sticky top-8 h-fit max-h-[calc(100vh-2rem)] overflow-y-auto rounded-3xl border border-slate-800/70 bg-slate-950 text-slate-100 shadow-2xl">
    <div class="border-b border-white/10 bg-linear-to-br from-slate-900 to-slate-950 p-6">
      <p class="text-xs font-bold uppercase tracking-[0.35em] text-cyan-300">SportLink</p>
      <h2 class="mt-3 text-2xl font-black">Console admin</h2>
      <p class="mt-2 text-sm leading-6 text-slate-400">Accedez a l analyse, la moderation et les operations systeme depuis un seul endroit.</p>
    </div>

    <nav class="p-4">
      <router-link
        v-for="item in navItems"
        :key="item.path"
        :to="item.path"
        class="group flex items-center gap-4 rounded-2xl border px-4 py-4 transition"
        :class="[
          isActive(item.path)
            ? 'border-cyan-400/30 bg-cyan-400/10 text-white shadow-lg shadow-cyan-500/10'
            : 'border-transparent text-slate-300 hover:border-white/10 hover:bg-white/5 hover:text-white'
        ]"
      >
        <span
          class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl transition"
          :class="[
            isActive(item.path)
              ? 'bg-cyan-400/20 text-cyan-200'
              : 'bg-white/5 text-slate-400 group-hover:bg-white/10 group-hover:text-white'
          ]"
        >
          <component :is="item.icon" class="h-5 w-5" />
        </span>

        <span class="min-w-0 flex-1">
          <span class="block text-sm font-semibold">{{ item.name }}</span>
          <span class="block text-xs leading-5 text-slate-400">{{ item.description }}</span>
        </span>
      </router-link>
    </nav>

    <div class="m-4 rounded-2xl border border-white/10 bg-white/5 p-4">
      <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Connecte en tant que</p>
      <p class="mt-2 text-sm font-semibold text-white">{{ displayName }}</p>
      <p class="text-xs text-slate-400">{{ displayRole }}</p>
    </div>
  </aside>
</template>

<script setup>
import { computed, h } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/AuthStore'

const route = useRoute()
const authStore = useAuthStore()

const createIcon = (pathData) => ({
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: pathData }),
  ]),
})

const OverviewIcon = createIcon('M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6')
const UsersIcon = createIcon('M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z')
const PostsIcon = createIcon('M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z')
const CommentsIcon = createIcon('M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z')
const ReportsIcon = createIcon('M12 21s8-4 8-10V5l-8-2-8 2v6c0 6 8 10 8 10z')

const navItems = [
  { name: 'Statistiques', description: 'Vue d ensemble de la plateforme', path: '/dashboard/admin/statistics', icon: OverviewIcon },
  { name: 'Utilisateurs', description: 'Comptes et roles', path: '/dashboard/admin/users', icon: UsersIcon },
  { name: 'Publications', description: 'Contenu publie', path: '/dashboard/admin/posts', icon: PostsIcon },
  { name: 'Commentaires', description: 'Fils de discussion', path: '/dashboard/admin/comments', icon: CommentsIcon },
  { name: 'Signalements', description: 'File de moderation', path: '/dashboard/admin/reports', icon: ReportsIcon },
]

const displayName = computed(() => authStore.user?.name || 'Administrateur')
const displayRole = computed(() => String(authStore.user?.role || 'ADMIN').toUpperCase())

const isActive = (path) => route.path === path || route.path.startsWith(`${path}/`)
</script>
