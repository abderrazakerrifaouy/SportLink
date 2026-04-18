import { createRouter, createWebHistory } from 'vue-router'


const LoginView = () => import("@/views/auth/LoginView.vue")
const RegisterView = () => import("@/views/auth/RegisterView.vue")
const HomeView = () => import("@/views/HomePage.vue")
const UserProfileView = () => import("@/views/UserProfileView.vue")
const PostDetailView = () => import("@/views/PostDetailView.vue")
const EditPostView = () => import("@/views/EditPostView.vue")
const MessagesView = () => import("@/views/MessagesView.vue")
const SearchView = () => import("@/views/SearchView.vue")
const PlayerProfileView = () => import("@/views/PlayerProfileView.vue")
const ClubAdminView = () => import("@/views/ClubAdminView.vue")
const SettingsView = () => import("@/views/SettingsView.vue")
const NotFoundView = () => import("@/views/NotFoundView.vue")


const routes = [
  {
    path: "/",
    name: "home",
    component: HomeView,
    meta: { requiresAuth: true }
  },
  {
    path: "/login",
    name: "login",
    component: LoginView,
    meta: { guest: true }
  },
  {
    path: "/register",
    name: "register",
    component: RegisterView,
    meta: { guest: true }
  },
  {
    path: "/users/:id",
    name: "userProfile",
    component: UserProfileView,
    meta: { requiresAuth: true }
  },
  {
    path: "/posts/:id",
    name: "postDetail",
    component: PostDetailView,
    meta: { requiresAuth: true }
  },
  {
    path: "/posts/:id/edit",
    name: "editPost",
    component: EditPostView,
    meta: { requiresAuth: true }
  },
  {
    path: "/messages",
    name: "messages",
    component: MessagesView,
    meta: { requiresAuth: true }
  },
  {
    path: "/messages/:userId",
    name: "messageUser",
    component: MessagesView,
    meta: { requiresAuth: true }
  },
  {
    path: "/search",
    name: "search",
    component: SearchView,
    meta: { requiresAuth: true }
  },
  {
    path: "/joueurs/:id",
    name: "playerProfile",
    component: PlayerProfileView,
    meta: { requiresAuth: true }
  },
  {
    path: "/clubs/:userId",
    name: "clubAdmin",
    component: ClubAdminView,
    meta: { requiresAuth: true }
  },
  {
    path: "/settings",
    name: "settings",
    component: SettingsView,
    meta: { requiresAuth: true }
  },
  {
    path: "/:pathMatch(.*)*",
    name: "notFound",
    component: NotFoundView
  }
]


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})


router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    return next('/login')
  }

  if (to.meta.guest && token) {
    return next('/')
  }

  next()
})

export default router
