import apiClient from '@/api/api'

export default {
  //  JOUEURS

  getAllJoueurs() {
    return apiClient.get('/joueurs')
  },

  getJoueur(id) {
    return apiClient.get(`/joueurs/${id}`)
  },

  createJoueur(data) {
    return apiClient.post('/joueurs', data)
  },

  //  EXPERIENCES

  getExperiencesByJoueurId(id) {
    return apiClient.get(`/joueurs/${id}/experiences`)
  },

  createExperience(data) {
    return apiClient.post('/experiences', data)
  },

  updateExperience(id, data) {
    return apiClient.put(`/experiences/${id}`, data)
  },

  deleteExperience(id) {
    return apiClient.delete(`/experiences/${id}`)
  },
}
