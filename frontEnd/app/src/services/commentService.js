import apiClient from './api'

export default {
  getCommentsByPostId: (postId) => apiClient.get(`/posts/${postId}/comments`),
  addComment: (postId, content) =>
    apiClient.post(`/posts/${postId}/comments`, { content }),
  getRepliesByCommentId: (commentId) =>
    apiClient.get(`/comments/${commentId}/replies`),
  addReply: (commentId, content) =>
    apiClient.post(`/comments/${commentId}/replies`, { content }),
  deleteComment: (id) => apiClient.delete(`/comments/${id}`),
  deleteReply: (id) => apiClient.delete(`/replies/${id}`),
}
