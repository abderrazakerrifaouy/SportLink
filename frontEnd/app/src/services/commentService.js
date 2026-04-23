import apiClient from '@/api/api'

export default {
  // --- COMMENTS ---
  getCommentsByPostId(postId) {
    return apiClient.get(`/posts/${postId}/comments`)
  },

  addComment(postId, content) {
    return apiClient.post(`/posts/${postId}/comments`, { content })
  },

  updateComment(commentId, data) {
    return apiClient.patch(`/comments/${commentId}`, data)
  },

  deleteComment(commentId) {
    return apiClient.delete(`/comments/${commentId}`)
  },

  // --- REPLIES ---
  getRepliesByCommentId(commentId) {
    return apiClient.get(`/comments/${commentId}/replies`)
  },

  addReply(commentId, content) {
    return apiClient.post(`/comments/${commentId}/replies`, { content })
  },

  updateReply(replyId, data) {
    return apiClient.patch(`/replies/${replyId}`, data)
  },

  deleteReply(replyId) {
    return apiClient.delete(`/replies/${replyId}`)
  }
}
