import apiClient from './api'

export default {
  // =====================
  // 📌 POSTS
  // =====================

  getAllPosts() {
    return apiClient.get('/posts')
  },

  getPostById(id) {
    return apiClient.get(`/posts/${id}`)
  },

  createPost(data) {
    return apiClient.post('/posts', data)
  },

  updatePost(id, data) {
    return apiClient.put(`/posts/${id}`, data)
  },

  deletePost(id) {
    return apiClient.delete(`/posts/${id}`)
  },

  getPostsByUserId(userId) {
    return apiClient.get(`/posts/user/${userId}`)
  },
}

