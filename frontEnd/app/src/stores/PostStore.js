import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import apiClient  from './../services/api'

export const  usePostStore = defineStore('post', () => {
  const posts = ref([])

  async function fetchPosts() {
    try {
      const response = await apiClient.get('/posts')
      posts.value = response.data
    } catch (error) {
      console.error('Failed to fetch posts:', error)
    }
  }

  async function createPost(content, media) {
    try {
      const response = await apiClient.post('/posts', { content, media })
      posts.value.unshift(response.data) // Ajouter le nouveau post en haut de la liste
    } catch (error) {
      console.error('Failed to create post:', error)
    }
  }

  return {
    posts,
    fetchPosts,
    createPost
  }
})
