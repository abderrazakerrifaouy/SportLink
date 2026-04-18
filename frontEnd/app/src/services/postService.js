import apiClient from './api'

export default {
  getAllPosts: () => apiClient.get('/posts'),
  getPostById: (id) => apiClient.get(`/posts/${id}`),
  createPost: (data) => apiClient.post('/posts', data),
  updatePost: (id, data) => apiClient.put(`/posts/${id}`, data),
  deletePost: (id) => apiClient.delete(`/posts/${id}`),
  getPostsByUserId: (userId) => apiClient.get(`/posts/user/${userId}`),
}
