import apiClient from './api'

export default {
  createReaction: (data) => apiClient.post('/reactions', data),
  deleteReaction: (id) => apiClient.delete(`/reactions/${id}`),
}
