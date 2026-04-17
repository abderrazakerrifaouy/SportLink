import { createRouter, createWebHistory } from 'vue-router'


const LoginView = () => import("@/views/auth/LoginView.vue")
const RegisterView = () => import("@/views/auth/RegisterView.vue")
const HomeView = () => import("@/views/HomePage.vue")


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
    path: "/:pathMatch(.*)*",
    redirect: "/login"
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
