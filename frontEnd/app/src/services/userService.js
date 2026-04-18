import apiClient from './api'

export default {
  getUsers: () => apiClient.get('/users'),
  getUser: (id) => apiClient.get(`/users/${id}`),
  updateUser: (id, data) => apiClient.put(`/users/${id}`, data),
  deleteUser: (id) => apiClient.delete(`/users/${id}`),
  searchByName: (name) => apiClient.get(`/users/search/${name}`),
  findByUsername: (username) => apiClient.get(`/users/username/${username}`),
  follow: (id) => apiClient.post(`/users/${id}/follow`),
  unfollow: (id) => apiClient.post(`/users/${id}/unfollow`),
  getFollowers: (id) => apiClient.get(`/users/${id}/followers`),
  getFollowing: (id) => apiClient.get(`/users/${id}/following`),
  getFollowersCount: (id) => apiClient.get(`/users/${id}/followers/count`),
  getFollowingCount: (id) => apiClient.get(`/users/${id}/following/count`),
  isFollowing: (id) => apiClient.get(`/users/${id}/is-following`),
  getConversations: () => apiClient.get('/users/conversations'),
  sendMessage: (receiverId, content) =>
    apiClient.post(`/users/${receiverId}/message`, { content }),
  getMessages: (userId1, userId2) =>
    apiClient.get(`/users/${userId1}/messages/${userId2}`),
  deleteMessage: (messageId) => apiClient.delete(`/users/messages/${messageId}`),
  updateMessage: (messageId, content) =>
    apiClient.put(`/users/update/messages/${messageId}`, { content }),
}
