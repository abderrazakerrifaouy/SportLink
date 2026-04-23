import { ref } from 'vue'
import { defineStore } from 'pinia'
import postService from '@/services/postService'
import reactionService from '@/services/reactionService'
import { useAuthStore } from './AuthStore'

export const usePostStore = defineStore('post', () => {
  const posts = ref([])
  const loading = ref(false)
  const authStore = useAuthStore()

  // --- Actions ---

  async function fetchPosts() {
    loading.value = true
    try {
      const response = await postService.getAllPosts()
      posts.value = response.data
    } catch (error) {
      console.error('Failed to fetch posts:', error)
    } finally {
      loading.value = false
    }
  }

  async function createPost(content, media = []) {
    try {
      // media format: [{url: '...', mediaType: 'IMAGE'}, ...]
      const response = await postService.createPost({ content, media })
      posts.value.unshift(response.data)
      return response.data
    } catch (error) {
      console.error('Failed to create post:', error)
    }
  }

  async function toggleReaction(postId, type) {
    if (!authStore.user || !type) return

    try {
      await reactionService.createReaction({
        type,
        reactable_id: postId,
        reactable_type: 'App\\Models\\Post'
      })

      const index = posts.value.findIndex(p => p.id === postId)
      if (index !== -1) {
        const updatedPost = await postService.getPostById(postId)
        posts.value[index] = updatedPost.data
      }
    } catch (error) {
      const validationMessage = reactionService.getValidationErrorMessage(error)
      if (validationMessage) {
        console.error('Reaction validation failed:', validationMessage)
      } else {
        console.error('Reaction toggle failed:', error)
      }

      // Keep UI consistent even after failed toggles.
      const index = posts.value.findIndex(p => p.id === postId)
      if (index !== -1) {
        try {
          const updatedPost = await postService.getPostById(postId)
          posts.value[index] = updatedPost.data
        } catch (syncError) {
          console.error('Failed to sync post after reaction error:', syncError)
        }
      }

      throw error
    }
  }

  async function deletePost(id) {
    try {
      await postService.deletePost(id)
      posts.value = posts.value.filter(p => p.id !== id)
    } catch (error) {
      console.error('Delete failed:', error)
    }
  }

  return { posts, loading, fetchPosts, createPost, toggleReaction, deletePost }
})
