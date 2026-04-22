import apiClient from '@/api/api'

export default {
  sendMessage(receiverId, content) {
    return apiClient.post(`/users/${receiverId}/message`, { content })
  },

  getMessages(userId1, userId2) {
    return apiClient.get(`/users/${userId1}/messages/${userId2}`)
  },

  getConversations() {
    return apiClient.get('/users/conversations')
  },

  getConversationByUser(userId) {
    return apiClient.get(`/users/conversation/${userId}`)
  },

  deleteMessage(messageId) {
    return apiClient.delete(`/users/messages/${messageId}`)
  },

  updateMessage(messageId, content) {
    return apiClient.put(`/users/update/messages/${messageId}`, { content })
  },
}