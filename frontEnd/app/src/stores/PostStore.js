import { ref } from 'vue'
import { defineStore } from 'pinia'
import postService from '@/services/postService'
import reactionService from '@/services/reactionService'

export const usePostStore = defineStore('post', () => {
  const posts = ref([])
  const loading = ref(false)

  const syncPost = async (postId) => {
    try {
      const response = await postService.getPostById(postId)
      const index = posts.value.findIndex((post) => String(post.id) === String(postId))
      if (index !== -1) {
        posts.value[index] = response.data
      }
      return response.data
    } catch (error) {
      console.error('Failed to sync post:', error)
      return null
    }
  }

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

  async function fetchPostsByUser(userId) {
    try {
      const response = await postService.getPostsByUserId(userId)
      return response.data
    } catch (error) {
      console.error('Failed to fetch posts by user:', error)
      throw error
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
      throw error
    }
  }

  async function toggleReaction(postId, type) {
    if (!type) return

    try {
      await reactionService.createReaction({
        type,
        reactable_id: postId,
        reactable_type: 'Post'
      })

      await syncPost(postId)
    } catch (error) {
      const validationMessage = reactionService.getValidationErrorMessage(error)
      if (error?.response?.status === 422) {
        console.error('Reaction validation failed:', validationMessage || 'Invalid reaction payload')
      } else if (validationMessage) {
        console.error('Reaction validation failed:', validationMessage)
      } else {
        console.error('Reaction toggle failed:', error)
      }

      await syncPost(postId)
    }
  }

  async function updatePost(id, data) {
    try {
      const response = await postService.updatePost(id, data)
      const index = posts.value.findIndex((post) => String(post.id) === String(id))
      if (index !== -1) {
        posts.value[index] = response.data
      }
      return response.data
    } catch (error) {
      console.error('Update failed:', error)
      throw error
    }
  }

  async function deletePost(id) {
    try {
      await postService.deletePost(id)
      posts.value = posts.value.filter((post) => String(post.id) !== String(id))
      return true
    } catch (error) {
      console.error('Delete failed:', error)
      throw error
    }
  }

  return { posts, loading, fetchPosts, fetchPostsByUser, createPost, toggleReaction, updatePost, deletePost, syncPost }
})
