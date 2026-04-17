import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import router from '@/router'
import apiClient  from './../services/api'

export const  useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)

  const isAuthenticated = computed(() => !!token.value)

  async function login(email, password) {
    try {
      const response = await apiClient.post('/login', {
        'email': email,
        'password': password
      })
      token.value = response.data.token
      localStorage.setItem('token', token.value)
      user.value = response.data.user
      console.log('Login successful:', user.value)
      router.push('/')
    } catch (error) {
      console.error('Login failed:', error)
      throw error
    }
  }

 // Zid l-aqwas {} f l-arguments bach n-destructuriw l-object
async function register({ name, email, password, password_confirmation, bio, profileImage, bannerImage, role }) {
  try {
    const formData = new FormData()

    // Kantakedo anna l-qiyam machi null/undefined qbel ma n-ajoutiwhoum (facultatif)
    formData.append('name', name || '')
    formData.append('email', email || '')
    formData.append('password', password || '')
    formData.append('password_confirmation', password_confirmation || '')
    formData.append('bio', bio || '')
    formData.append('role', role || '')

    // Ila kanti ghadi tsift tsawer, khass n-checkiw wach kaynin
    if (profileImage) formData.append('profileImage', profileImage)
    if (bannerImage) formData.append('bannerImage', bannerImage)

    console.log('le formData content:')
    for (let pair of formData.entries()) {
        console.log(pair[0]+ ': ' + pair[1]);
    }


    const response = await apiClient.post('/register', formData)

    token.value = response.data.token
    localStorage.setItem('token', token.value)
    user.value = response.data.user

    router.push('/')
  } catch (error) {
    console.error('Registration failed:', error)
    throw error
  }
}

  async function fetchUser() {
    if (!token.value) return
    if (user.value) return
    try {
      const response = await apiClient.get('/user/authenticated' ,
        {headers: {
          Authorization: `Bearer ${token.value}`
        }
      }
      )
      user.value = response.data
      console.log('Fetched user:', user.value)
    } catch (error) {
      console.error('Failed to fetch user:', error)
      logout() // Ila kayn chi mouchkil f fetch, n-logoutiw l-user

    }
  }

  function logout() {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
    router.push('/login')
  }


  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout,
    fetchUser
  }
})
