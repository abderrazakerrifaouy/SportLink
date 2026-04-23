import { ref } from 'vue'
import { defineStore } from 'pinia'
import commentService from '@/services/commentService'
import { usePostStore } from './PostStore'

export const useCommentStore = defineStore('comment', () => {
  const commentsByPost = ref({}) // Format: { postId: [comments] }
  const postStore = usePostStore()

  const getComments = (postId) => commentsByPost.value[postId] || []

  async function fetchComments(postId) {
    try {
      const response = await commentService.getCommentsByPostId(postId)
      commentsByPost.value[postId] = response.data
    } catch (error) {
      console.error('Fetch comments failed:', error)
    }
  }

  async function addComment(postId, content) {
    try {
      const response = await commentService.addComment(postId, content)

      // Update local comments list
      if (!commentsByPost.value[postId]) commentsByPost.value[postId] = []
      commentsByPost.value[postId].unshift(response.data)

      // Sync l-counter li f PostStore
      const post = postStore.posts.find(p => p.id === postId)
      if (post) post.comments_count++

      return response.data
    } catch (error) {
      console.error('Add comment failed:', error)
      throw error
    }
  }

  return { commentsByPost, getComments, fetchComments, addComment }
})
