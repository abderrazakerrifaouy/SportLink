import apiClient from '@/api/api'

export default {
  /**
   * Toggle a reaction on a polymorphic target.
   * Backend behavior:
   * - create if no reaction exists
   * - remove if same type exists
   * - update if a different type exists
   *
   * @param {Object} data - { type, reactable_id, reactable_type }
   */
  createReaction(data) {
    return apiClient.post('/reactions', data)
  },

  getValidationErrorMessage(error) {
    const response = error?.response
    if (!response || response.status !== 422) return null

    const payload = response.data || {}
    if (payload.message) return payload.message

    const firstField = Object.keys(payload.errors || {})[0]
    if (firstField && payload.errors[firstField]?.length) {
      return payload.errors[firstField][0]
    }

    return 'Validation failed while submitting reaction.'
  },

  deleteReaction(id) {
    return apiClient.delete(`/reactions/${id}`)
  }
}
