import api from "@/api/api";

export default {
  
  getClubAdmins: () => apiClient.get('/club-admins'),

  createClubAdmin: (data) => apiClient.post('/club-admins', data),

  getClubAdminByUserId: (userId) => apiClient.get(`/club-admins/user/${userId}`),

  updateClubAdmin: (data) => apiClient.put(`/club-admins/user`, data),

  deleteClubAdmin: (userId) => apiClient.delete(`/club-admins/user/${userId}`),

  searchByName: (name) => apiClient.get(`/club-admins/search/${name}`),

  getTitresByClubAdminId: (clubAdminId) => apiClient.get(`/club-admins/${clubAdminId}/titres`),

  showTitre: (titreId) => apiClient.get(`/titres/${titreId}`),

  createTitre: (data) => apiClient.post('/titres', data),

  deleteTitre: (id) => apiClient.delete(`/titres/${id}`),

  updateTitre: (id, data) => apiClient.put(`/titres/${id}`, data),

  getAllTitres: () => apiClient.get('/titres'),

  getTitre: (id) => apiClient.get(`/titres/${id}`),
}
