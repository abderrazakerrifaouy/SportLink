import { ref } from 'vue'
import { defineStore } from 'pinia'
import apiClient from './../services/api'
import postService from './../services/postService'

export const usePostStore = defineStore('post', () => {
  const posts = ref([])
  const currentPost = ref(null)

  async function fetchPosts() {
    try {
      const response = await postService.getAllPosts()
      posts.value = response.data
      return response.data
    } catch (error) {
      console.error('Failed to fetch posts:', error)
      throw error
    }
  }

  async function fetchPostById(id) {
    try {
      const response = await postService.getPostById(id)
      currentPost.value = response.data
      return response.data
    } catch (error) {
      console.error('Failed to fetch post:', error)
      throw error
    }
  }

  async function fetchPostsByUser(userId) {
    try {
      const response = await postService.getPostsByUserId(userId)
      return response.data
    } catch (error) {
      console.error('Failed to fetch user posts:', error)
      throw error
    }
  }

  async function createPost(content, media) {
    try {
      const response = await postService.createPost({ content, media })
      posts.value.unshift(response.data)
      return response.data
    } catch (error) {
      console.error('Failed to create post:', error)
      throw error
    }
  }

  async function updatePost(id, content, media) {
    try {
      const response = await postService.updatePost(id, { content, media })
      const index = posts.value.findIndex((p) => p.id === id)
      if (index !== -1) posts.value[index] = response.data
      if (currentPost.value?.id === id) currentPost.value = response.data
      return response.data
    } catch (error) {
      console.error('Failed to update post:', error)
      throw error
    }
  }

  async function deletePost(id) {
    try {
      await postService.deletePost(id)
      posts.value = posts.value.filter((p) => p.id !== id)
      if (currentPost.value?.id === id) currentPost.value = null
    } catch (error) {
      console.error('Failed to delete post:', error)
      throw error
    }
  }

  return {
    posts,
    currentPost,
    fetchPosts,
    fetchPostById,
    fetchPostsByUser,
    createPost,
    updatePost,
    deletePost,
  }
})
