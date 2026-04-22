import apiClient from '@/api/api'

export default {
  // COMMENTS

  getCommentsByPostId(postId) {
    return apiClient.get(`/posts/${postId}/comments`)
  },

  addComment(postId, content) {
    return apiClient.post(`/posts/${postId}/comments`, { content })
  },

  deleteComment(commentId) {
    return apiClient.delete(`/comments/${commentId}`)
  },

  //  REPLIES

  getRepliesByCommentId(commentId) {
    return apiClient.get(`/comments/${commentId}/replies`)
  },

  addReply(commentId, content) {
    return apiClient.post(`/comments/${commentId}/replies`, { content })
  },

  deleteReply(replyId) {
    return apiClient.delete(`/replies/${replyId}`)
  },
}


