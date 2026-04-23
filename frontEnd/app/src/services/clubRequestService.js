import apiClient from '@/api/api'

export default {
  getHistory: (scope) => apiClient.get('/club-joueur-requests', { params: scope ? { scope } : {} }),

  acceptRequest: (id) => apiClient.post(`/club-joueur-requests/${id}/accept`),

  rejectRequest: (id) => apiClient.post(`/club-joueur-requests/${id}/reject`),
}
