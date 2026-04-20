import { ref } from 'vue'
import { defineStore } from 'pinia'
import router from '@/router'
import api from '@/api/api'

export const useAuthStore = defineStore('auth', () => {

  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)

  // 🔥 reactive form data (for inputs)
  const formData = ref({
    name: '',
    email: '',
    role: '',
    password: '',
    password_confirmation: '',
    bio: '',
    profileImage: null,
    bannerImage: null,
  })


  async function login(credentials) {
    try {
      const response = await api.post('/login', credentials)

      token.value = response.data.token
      user.value = response.data.user

      localStorage.setItem('token', token.value)

      router.push('/')
    } catch (error) {
      console.error('Login failed:', error)
      throw error
    }
  }

  async function register() {
    console.log('Registering with data:', formData.value)
    try {
      const fd = new FormData()

      fd.append('name', formData.value.name)
      fd.append('email', formData.value.email)
      fd.append('role', formData.value.role)
      fd.append('password', formData.value.password)
      fd.append('password_confirmation', formData.value.password_confirmation)
      fd.append('bio', formData.value.bio)

      if (formData.value.profileImage) {
        fd.append('profileImage', formData.value.profile_image)
      }

      if (formData.value.bannerImage) {
        fd.append('bannerImage', formData.value.banner_image)
      }

      const response = await api.post('/register', fd, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })

      token.value = response.data.token
      user.value = response.data.user

      localStorage.setItem('token', token.value)

      router.push('/')
    } catch (error) {
      console.error('Registration failed:', error)
      throw error
    }
  }


  function setField(field, value) {
    formData.value[field] = value
  }

  function setProfileImage(file) {
    formData.value.profileImage = file
  }

  function setBannerImage(file) {
    formData.value.bannerImage = file
  }


  function fetchUser() {
    if (!token.value) return

    api.get('/user/authenticated')
      .then(response => {
        user.value = response.data
      })
      .catch(error => {
        console.error('Failed to fetch user:', error)
        logout()
      })
  }

  function logout() {
    token.value = null
    user.value = null
    localStorage.removeItem('token')

    router.push('/auth/login')
  }

  // =====================
  // RETURN
  // =====================
  return {
    user,
    token,
    formData,

    login,
    register,
    logout,
    fetchUser,

    setField,
    setProfileImage,
    setBannerImage,
  }
})
