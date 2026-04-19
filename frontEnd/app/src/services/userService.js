import apiClient from './api'

export default {
  //  USERS

  getUsers() {
    return apiClient.get('/users')
  },

  getUser(id) {
    return apiClient.get(`/users/${id}`)
  },

  updateUser(id, data) {
    return apiClient.put(`/users/${id}`, data)
  },

  deleteUser(id) {
    return apiClient.delete(`/users/${id}`)
  },

  searchUsers(name) {
    return apiClient.get(`/users/search/${name}`)
  },

  //  MESSAGES

  sendMessage(receiverId, content) {
    return apiClient.post(`/users/${receiverId}/message`, { content })
  },

  getMessages(userId1, userId2) {
    return apiClient.get(`/users/${userId1}/messages/${userId2}`)
  },

  getConversations() {
    return apiClient.get('/users/conversations')
  },

  deleteMessage(messageId) {
    return apiClient.delete(`/users/messages/${messageId}`)
  },

  updateMessage(messageId, content) {
    return apiClient.put(`/users/update/messages/${messageId}`, { content })
  },

  //  FOLLOWS

  followUser(userId) {
    return apiClient.post(`/users/${userId}/follow`)
  },

  unfollowUser(userId) {
    return apiClient.delete(`/users/${userId}/unfollow`)
  },

  getFollowers(userId) {
    return apiClient.get(`/users/${userId}/followers`)
  },

  getFollowing(userId) {
    return apiClient.get(`/users/${userId}/following`)
  },

  getFollowersCount(userId) {
    return apiClient.get(`/users/${userId}/followers/count`)
  },

  getFollowingCount(userId) {
    return apiClient.get(`/users/${userId}/following/count`)
  },

  isFollowing(userId) {
    return apiClient.get(`/users/${userId}/is-following`)
  },
}
