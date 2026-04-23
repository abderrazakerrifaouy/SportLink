import apiClient from '@/api/api'

export default {
  getHistory: (scope) => apiClient.get('/club-joueur-requests', { params: scope ? { scope } : {} }),

  getMyClub: () => apiClient.get('/player/my-club'),

  leaveMyClub: () => apiClient.post('/player/leave-club'),

  acceptRequest: (id, payload = {}) => apiClient.post(`/club-joueur-requests/${id}/accept`, payload),

  rejectRequest: (id) => apiClient.post(`/club-joueur-requests/${id}/reject`),
}
