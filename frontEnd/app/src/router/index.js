import { createRouter, createWebHistory } from 'vue-router'
import AuthLayout from '../layouts/AuthLayout.vue'
import Login from '@/views/Auth/Login.vue'
import Register from '@/views/Auth/Register.vue'
import MainLayout from '@/layouts/mainLayout.vue'
import HomePage from '@/views/HomePage.vue'
import ProfilePage from '@/views/ProfilePage.vue'
import MessagesPage from '@/views/MessagesPage.vue'
import UsersPage from '@/views/UsersPage.vue'
import NetworkPage from '@/views/NetworkPage.vue'
import ClubsPage from '@/views/ClubsPage.vue'
import PlayersPage from '@/views/PlayersPage.vue'
import PlayerClubRequests from '@/views/player/ClubRequests.vue'
import PlayerExperiences from '@/views/player/Experiences.vue'
import PlayerMyClub from '@/views/player/MyClub.vue'
import PlayerDetails from '@/views/player/PlayerDetails.vue'
import ClubAdminPlayers from '@/views/club/Players.vue'
import ClubAdminRequests from '@/views/club/ClubRequests.vue'
import ClubAdminTitles from '@/views/club/Titles.vue'
import TitleDetails from '@/views/club/TitleDetails.vue'
import ClubDetails from '@/views/club/ClubDetails.vue'
import AdminDashboard from '@/views/admin/Dashboard.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/auth',
      component: AuthLayout,
      meta: { guest: true },
      children: [
        { path: 'login', name: 'Login', component: Login },
        { path: 'register', name: 'Register', component: Register },
      ],
    },
    {
      path: '/dashboard',
      component: MainLayout,
      meta: { requiresAuth: true },
      children: [
        { path: '', name: 'Home', component: HomePage },
        { path: 'profile', name: 'Profile', component: ProfilePage },
        { path: 'profile/:id', name: 'UserProfile', component: ProfilePage },
        { path: 'messages', name: 'Messages', component: MessagesPage },
        { path: 'users', name: 'Users', component: UsersPage },
        { path: 'network', name: 'Network', component: NetworkPage },
        { path: 'clubs', name: 'Clubs', component: ClubsPage },
        { path: 'players', name: 'Players', component: PlayersPage },
        { path: 'player/:id', name: 'PlayerDetails', component: PlayerDetails },
        { path: 'player/club-requests', name: 'PlayerClubRequests', component: PlayerClubRequests },
        { path: 'player/experiences', name: 'PlayerExperiences', component: PlayerExperiences },
        { path: 'player/my-club', name: 'PlayerMyClub', component: PlayerMyClub },
        { path: 'club-admin/players', name: 'ClubAdminPlayers', component: ClubAdminPlayers },
        { path: 'club-admin/club-requests', name: 'ClubAdminRequests', component: ClubAdminRequests },
        { path: 'club-admin/titles', name: 'ClubAdminTitles', component: ClubAdminTitles },
        { path: 'club-admin/titles/:id', name: 'TitleDetails', component: TitleDetails },
        { path: 'club/:id', name: 'ClubDetails', component: ClubDetails },
        { path: 'admin/dashboard', name: 'AdminDashboard', component: AdminDashboard },
      ],
    },
    { path: '/', redirect: '/dashboard' }
  ],
})

router.beforeEach((to, from) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    return { name: 'Login' }
  }

  if (to.meta.guest && token) {
    return { name: 'Home' }
  }

})

export default router
