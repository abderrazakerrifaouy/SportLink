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
import AdminClupDashboard from '@/views/club/Dashboard.vue'
import AdminUsers from '@/views/admin/AdminUsers.vue'
import AdminPosts from '@/views/admin/AdminPosts.vue'
import AdminComments from '@/views/admin/AdminComments.vue'
import AdminReports from '@/views/admin/AdminReports.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // مسارات التسجيل والدخول
    {
      path: '/auth',
      component: AuthLayout,
      meta: { requiresGuest: true },
      children: [
        { path: 'login', name: 'Login', component: Login },
        { path: 'register', name: 'Register', component: Register },
      ],
    },
    // مسارات لوحة التحكم والصفحات الرئيسية
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

        // مسارات اللاعبين (Player)
        { path: 'player/:id', name: 'PlayerDetails', component: PlayerDetails },
        { path: 'player/club-requests', name: 'PlayerClubRequests', component: PlayerClubRequests },
        { path: 'player/experiences', name: 'PlayerExperiences', component: PlayerExperiences },
        { path: 'player/my-club', name: 'PlayerMyClub', component: PlayerMyClub },

        // مسارات إدارة النادي (Club Admin)
        { path: 'club-admin/players', name: 'ClubAdminPlayers', component: ClubAdminPlayers },
        { path: 'club-admin/club-requests', name: 'ClubAdminRequests', component: ClubAdminRequests },
        { path: 'club-admin/titles', name: 'ClubAdminTitles', component: ClubAdminTitles },
        { path: 'club-admin/titles/:id', name: 'TitleDetails', component: TitleDetails },
        { path: 'club/:id', name: 'ClubDetails', component: ClubDetails },
        { path: 'admin_clup/dashboard', name: 'AdminClupDashboard', component: AdminClupDashboard },

        // مسارات المدير العام (System Admin)
        { path: 'admin/dashboard', name: 'AdminDashboard', component: AdminClupDashboard },
        { path: 'admin/users', name: 'AdminUsers', component: AdminUsers },
        { path: 'admin/posts', name: 'AdminPosts', component: AdminPosts },
        { path: 'admin/comments', name: 'AdminComments', component: AdminComments },
        { path: 'admin/reports', name: 'AdminReports', component: AdminReports },
      ],
    },
    // إعادة التوجيه الافتراضي
    { path: '/', redirect: '/dashboard' },
    { path: '/:pathMatch(.*)*', redirect: '/dashboard' },
  ],
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!token) {
      next({ name: 'Login' })
    } else {
      next()
    }
  } else if (to.matched.some(record => record.meta.requiresGuest)) {
    if (token) {
      next({ name: 'Home' })
    } else {
      next()
    }
  } else {
    next()
  }
})

export default router
