import apiClient from './api'

export default {
  getJoueurs: () => apiClient.get('/joueurs'),
  getJoueur: (id) => apiClient.get(`/joueurs/${id}`),
  createJoueur: (data) => apiClient.post('/joueurs', data),
  getExperiencesByJoueurId: (id) =>
    apiClient.get(`/joueurs/${id}/experiences`),
  createExperience: (data) => apiClient.post('/experiences', data),
  updateExperience: (id, data) => apiClient.put(`/experiences/${id}`, data),
  deleteExperience: (id) => apiClient.delete(`/experiences/${id}`),
}
