<template>
  <aside class="fixed left-0 top-16 bottom-0 w-64 bg-white border-r border-gray-100 overflow-y-auto hidden lg:block custom-scrollbar">
    <nav class="p-4 space-y-1">
      <template v-for="(item, index) in filteredMenuItems" :key="index">

        <div v-if="item.isHeader" class="pt-5 pb-2">
          <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-widest">
            {{ item.name }}
          </p>
        </div>

        <router-link
          v-else
          :to="item.path"
          class="group flex items-center px-3 py-2 text-sm font-medium rounded-xl transition-all duration-200"
          :class="[
            isActive(item.path)
              ? 'bg-blue-50 text-blue-600 shadow-sm'
              : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
          ]"
        >
          <component
            :is="item.icon"
            class="w-5 h-5 mr-3 transition-colors"
            :class="[
              isActive(item.path) ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-600'
            ]"
          />
          <span :class="isActive(item.path) ? 'font-semibold' : ''">{{ item.name }}</span>

          <span
            v-if="item.name === 'Messages' && messagesBadge.badge"
            class="ml-auto px-2 py-0.5 text-[10px] font-bold rounded-full"
            :class="messagesBadge.badgeClass"
          >
            {{ messagesBadge.badge }}
          </span>
          <span
            v-else-if="item.badge"
            class="ml-auto px-2 py-0.5 text-[10px] font-bold rounded-full"
            :class="item.badgeClass || 'bg-blue-100 text-blue-600'"
          >
            {{ item.badge }}
          </span>
        </router-link>
      </template>
    </nav>
  </aside>
</template>

<script setup>
import { computed, h, ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/AuthStore'
import { useUserStore } from '@/stores/userStore'

const userStore = useUserStore()
const unreadMessages = ref(0)

const HomeIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' })
  ])
}

const MessageIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z' })
  ])
}

const UserIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' })
  ])
}

const UsersIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' })
  ])
}

const NetworkIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
  ])
}

const ClubIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' })
  ])
}

const PlayerIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
  ])
}

const RequestIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' })
  ])
}

const ExperienceIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z' })
  ])
}

const TrophyIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z' })
  ])
}
const DashboardIcon = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' })
  ])
}

const route = useRoute()
const auth = useAuthStore()

const menuItemsBase = [
  { name: 'Accueil', path: '/dashboard', icon: HomeIcon },
  { name: 'Messages', path: '/dashboard/messages', icon: MessageIcon },
  { name: 'Profil', path: '/dashboard/profile', icon: UserIcon },
  { name: 'Utilisateurs', path: '/dashboard/users', icon: UsersIcon },
  { name: 'Mon reseau', path: '/dashboard/network', icon: NetworkIcon },
  { name: 'Clubs', path: '/dashboard/clubs', icon: ClubIcon },
  { name: 'Joueurs', path: '/dashboard/players', icon: PlayerIcon },
]

const menuItemsPlayer = [
  { name: 'Mon club', path: '/dashboard/player/my-club', icon: ClubIcon },
  { name: 'Demandes de club', path: '/dashboard/player/club-requests', icon: RequestIcon },
]

const menuItemsClubAdmin = [
  { name: 'Tableau de bord club', path: '/dashboard/admin_clup/dashboard', icon: DashboardIcon },
  { name: 'Mes joueurs', path: '/dashboard/club-admin/players', icon: PlayerIcon },
  { name: 'Demandes du club', path: '/dashboard/club-admin/club-requests', icon: RequestIcon },
  { name: 'Mes titres', path: '/dashboard/club-admin/titles', icon: TrophyIcon },
]

const menuItemsGlobalAdmin = [
  { name: 'Gerer utilisateurs', path: '/dashboard/admin/users', icon: UsersIcon },
  { name: 'Gerer publications', path: '/dashboard/admin/posts', icon: HomeIcon },
  { name: 'Gerer commentaires', path: '/dashboard/admin/comments', icon: MessageIcon },
  { name: 'Signalements', path: '/dashboard/admin/reports', icon: RequestIcon },
]

const messagesBadge = computed(() => {
  if (unreadMessages.value > 0) {
    return { badge: unreadMessages.value > 99 ? '99+' : unreadMessages.value.toString(), badgeClass: 'bg-red-500 text-white' }
  }
  return { badge: null }
})

const fetchUnreadCount = async () => {
  try {
    await userStore.fetchConversations()
    const unread = userStore.conversations.filter(c => c.unread && c.unread > 0)
    unreadMessages.value = unread.length
  } catch (error) {
    console.error('Failed to fetch unread count:', error)
  }
}

onMounted(() => {
  fetchUnreadCount()
})

const normalizedRole = computed(() => {
  const persistedRole = (() => {
    try {
      return JSON.parse(localStorage.getItem('user') || '{}')?.role || ''
    } catch (error) {
      return ''
    }
  })()

  return String(auth.user?.role || persistedRole).toUpperCase()
})

const menuItems = computed(() => {
  const role = normalizedRole.value
  const isPlayer =  role === 'JOUEUR'
  const isClubAdmin = role === 'CLUB_ADMIN' 
  const isGlobalAdmin = role === 'ADMIN'

  const items = [
    { isHeader: true, name: 'General' },
    ...menuItemsBase,
  ]

  if (isPlayer) {
    items.push({ isHeader: true, name: 'Joueur' })
    items.push(...menuItemsPlayer)
  }

  if (isClubAdmin) {
    items.push({ isHeader: true, name: 'Admin club' })
    items.push(...menuItemsClubAdmin)
  }

  if (isGlobalAdmin) {
    items.push({ isHeader: true, name: 'Admin global' })
    items.push(...menuItemsGlobalAdmin)
  }

  return items
})

const filteredMenuItems = computed(() => {
  return menuItems.value
})

const isActive = (path) => {
  if (path === '/dashboard') {
    return route.path === '/dashboard' || route.path === '/dashboard/'
  }
  return route.path === path || route.path.startsWith(`${path}/`)
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #f1f1f1;
  border-radius: 10px;
}
.custom-scrollbar:hover::-webkit-scrollbar-thumb {
  background: #e5e7eb;
}
</style>
