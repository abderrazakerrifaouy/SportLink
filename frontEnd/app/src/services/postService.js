import apiClient from '@/api/api'

export default {
  getAllPosts() {
    return apiClient.get('/posts')
  },

  getPostById(id) {
    return apiClient.get(`/posts/${id}`)
  },

  createPost(data) {
    // Ssefet JSON pur hitach media URLs strings
    // data khass ykon fih { content, media: [{url, mediaType}, ...] }
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
  }
}
