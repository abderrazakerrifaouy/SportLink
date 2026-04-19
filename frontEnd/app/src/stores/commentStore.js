import { ref } from 'vue'
import { defineStore } from 'pinia'
import commentService from '@/services/commentService'

export const useCommentStore = defineStore('comment', () => {
  const comments = ref([])
  const replies = ref({})

  const fetchComments = async (postId) => {
    const response = await commentService.getCommentsByPostId(postId)
    comments.value = response.data
    return response.data
  }

  const addComment = async (postId, content) => {
    const response = await commentService.addComment(postId, content)
    comments.value.push(response.data)
    return response.data
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

  const deleteComment = async (id) => {
    await commentService.deleteComment(id)
    comments.value = comments.value.filter((c) => c.id !== id)
  }

  const deleteReply = async (commentId, replyId) => {
    await commentService.deleteReply(replyId)
    if (replies.value[commentId]) {
      replies.value[commentId] = replies.value[commentId].filter(
        (r) => r.id !== replyId
      )
    }
  }

  const reset = () => {
    comments.value = []
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
    reset,
  }
})
