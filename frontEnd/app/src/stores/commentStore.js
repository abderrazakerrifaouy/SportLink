import { ref } from 'vue'
import { defineStore } from 'pinia'
import commentService from '@/services/commentService'
import { useAuthStore } from './AuthStore'

export const useCommentStore = defineStore('comment', () => {
  const comments = ref({})
  const replies = ref({})

  const fetchComments = async (postId) => {
    const response = await commentService.getCommentsByPostId(postId)
    comments.value[postId] = response.data
    return response.data
  }

  const addComment = async (postId, content) => {
    const authStore = useAuthStore()
    const response = await commentService.addComment(postId, content)
    
    const newComment = {
      ...response.data,
      user: authStore.user ? {
        id: authStore.user.id,
        name: authStore.user.name,
        profileImage: authStore.user.profileImage,
        profile_image: authStore.user.profile_image
      } : null
    }
    
    if (!comments.value[postId]) {
      comments.value[postId] = []
    }
    comments.value[postId].unshift(newComment)
    return newComment
  }

  const fetchReplies = async (commentId) => {
    const response = await commentService.getRepliesByCommentId(commentId)
    replies.value[commentId] = response.data
    return response.data
  }

  const addReply = async (commentId, content) => {
    const response = await commentService.addReply(commentId, content)
    if (!replies.value[commentId]) replies.value[commentId] = []
    replies.value[commentId].push(response.data)
    return response.data
  }

  const deleteComment = async (postId, commentId) => {
    await commentService.deleteComment(commentId)
    if (comments.value[postId]) {
      comments.value[postId] = comments.value[postId].filter((c) => c.id !== commentId)
    }
  }

  const deleteReply = async (commentId, replyId) => {
    await commentService.deleteReply(replyId)
    if (replies.value[commentId]) {
      replies.value[commentId] = replies.value[commentId].filter(
        (r) => r.id !== replyId
      )
    }
  }

  const getComments = (postId) => {
    return comments.value[postId] || []
  }

  const getReplies = (commentId) => {
    return replies.value[commentId] || []
  }

  const reset = () => {
    comments.value = {}
    replies.value = {}
  }

  return {
    comments,
    replies,
    fetchComments,
    addComment,
    fetchReplies,
    addReply,
    deleteComment,
    deleteReply,
    getComments,
    getReplies,
    reset,
  }
})
