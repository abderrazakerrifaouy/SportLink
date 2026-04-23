import apiClient from "@/api/api";

export default {

  getClubAdmins: () => apiClient.get('/club-admins'),

  createClubAdmin: (data) => apiClient.post('/club-admins', data),

  getClubAdminByUserId: (userId) => apiClient.get(`/club-admins/user/${userId}`),

  updateClubAdmin: (userId, data) => apiClient.put(`/club-admins/user/${userId}`, data),

  deleteClubAdmin: (userId) => apiClient.delete(`/club-admins/user/${userId}`),

  searchByName: (name) => apiClient.get(`/club-admins/search/${name}`),

  getTitresByClubAdminId: (clubAdminId) => apiClient.get(`/club-admins/${clubAdminId}/titres`),

  createTitre: (data) => apiClient.post('/titres', data),

  deleteTitre: (id) => apiClient.delete(`/titres/${id}`),

  updateTitre: (id, data) => apiClient.put(`/titres/${id}`, data),

  getAllTitres: () => apiClient.get('/titres'),

  getTitre: (id) => apiClient.get(`/titres/${id}`),

  getClubAdminById: (id) => apiClient.get(`/club-admins/${id}`),

  clubAdminExists: (userId) => apiClient.get(`/club-admins/exists/${userId}`),

  invitePlayerToClub: (joueurId) => apiClient.post('/club-admins/invite-player', { joueur_id: joueurId }),
}

