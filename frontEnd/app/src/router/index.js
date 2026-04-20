import { createRouter, createWebHistory } from 'vue-router'
import AuthLayout from '../layouts/AuthLayout.vue'
import Login from '../views/Auth/Login.vue'
import Register from '../views/Auth/Register.vue'
import MainLayout from '@/layouts/mainLayout.vue'
import HomePage from '@/views/HomePage.vue'




const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
    path: '/auth',
    component: AuthLayout,
    meta: { guest: true },
    children: [
      {
        path: 'login',
        name: 'Login',
        component: Login,
      },
      {
        path: 'register',
        name: 'Register',
        component: Register,
      },
    ],
  },
    {
      path: '/dashboard',
      component: MainLayout,
      meta: { requiresAuth: true } ,
      children: [
        {
          path: '',
          name: 'Home',
          component: HomePage,
        }
      ]

    },

  ],
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    return next('/auth/login')
  }

  if (to.meta.guest && token) {
    return next('/dashboard')
  }

  next()
})

export default router
