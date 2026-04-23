import apiClient from '@/api/api'

export default {
  /**
   * Create or update a reaction
   * @param {Object} data - { type, reactable_id, reactable_type }
   * reactable_type ssefet fih "App\\Models\\Post" aw "App\\Models\\Comment"
   */
  createReaction(data) {
    return apiClient.post('/reactions', data)
  },

  deleteReaction(id) {
    return apiClient.delete(`/reactions/${id}`)
  }
}
