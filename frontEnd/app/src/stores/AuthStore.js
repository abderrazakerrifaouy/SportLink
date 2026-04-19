import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import router from '@/router'
import api  from '@/api/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)
  const formData = new FormData()


  async function login(credentials) {
    try {
      const response = await api.post('/auth/login', credentials)
      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem('token', token.value)
      router.push('/')
    } catch (error) {
      console.error('Login failed:', error)
      throw error
    }
  }
  
  function setRole(role) {
    formData.append('role', role)
  }
  function setProfileImage(file) {
    formData.append('profileImage', file)
  }
  function setBannerImage(file) {
    formData.append('bannerImage', file)
  }
  function setName(name) {
    formData.append('name', name)
  }
  function setEmail(email) {
    formData.append('email', email)
  }
  function setPassword(password , passwordConfirmation) {
    formData.append('password', password)
    formData.append('password_confirmation', passwordConfirmation)

  }
  function setBio(bio) {
    formData.append('bio', bio)
  }

  async function register() {
    try {
      const response = await api.post('/auth/register', formData, {
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

  function logout() {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    router.push('/login')
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


  return {
    user,
    token,
    fetchUser,
    login,
    register,
    logout,
    setRole,
    setProfileImage,
    setBannerImage,
    setName,
    setEmail,
    setPassword,
    setBio
  }
})
