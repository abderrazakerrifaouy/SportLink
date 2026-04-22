import apiClient from '@/api/api'

export default {
  //  REACTIONS

  createReaction(data) {
    // data = { type, reactable_id, reactable_type }
    return apiClient.post('/reactions', data)
  },

  deleteReaction(id) {
    return apiClient.delete(`/reactions/${id}`)
  },
  //  OPTIONAL (upgrade-ready)
  toggleReaction(data) {
    return apiClient.post('/reactions/toggle', data)
  },
}
