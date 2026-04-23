import { ref } from 'vue'
import { defineStore } from 'pinia'
import  clubAdminService  from '@/services/cloubAdminService'

export const useClubAdminStore = defineStore('clubAdmin', () => {
  const clubAdmins = ref([])
  const currentClubAdmin = ref(null)
  const titres = ref([])
  const searchResults = ref([])

  const fetchClubAdmins = async () => {
    const response = await clubAdminService.getClubAdmins()
    clubAdmins.value = response.data
    return response.data
  }

  const fetchClubAdminByUserId = async (userId) => {
    const response = await clubAdminService.getClubAdminByUserId(userId)
    currentClubAdmin.value = response.data
    return response.data
  }


  const createClubAdmin = async (data) => {
    const response = await clubAdminService.createClubAdmin(data)
    currentClubAdmin.value = response.data
    return response.data
  }

  const updateClubAdmin = async (userId, data) => {
    const response = await clubAdminService.updateClubAdmin(userId, data)
    currentClubAdmin.value = response.data
    return response.data
  }

  const deleteClubAdmin = async (userId) => {
    await clubAdminService.deleteClubAdmin(userId)
    currentClubAdmin.value = null
  }

  const searchClubAdmins = async (name) => {
    const response = await clubAdminService.searchByName(name)
    searchResults.value = response.data
    return response.data
  }

  const fetchTitres = async (clubAdminId) => {
    const response = await clubAdminService.getTitresByClubAdminId(clubAdminId)
    titres.value = response.data
    return response.data
  }

  const createTitre = async (data) => {
    const response = await clubAdminService.createTitre(data)
    titres.value.push(response.data)
    return response.data
  }

  const updateTitre = async (id, data) => {
    const response = await clubAdminService.updateTitre(id, data)
    const index = titres.value.findIndex((t) => t.id === id)
    if (index !== -1) titres.value[index] = response.data
    return response.data
  }

  const deleteTitre = async (id) => {
    await clubAdminService.deleteTitre(id)
    titres.value = titres.value.filter((t) => t.id !== id)
  }

  const clubAdminExists = async (userId) => {
    const response = await clubAdminService.clubAdminExists(userId)
    return response.data.exists
  }

  return {
    clubAdmins,
    currentClubAdmin,
    titres,
    searchResults,
    fetchClubAdmins,
    fetchClubAdminByUserId,
    createClubAdmin,
    updateClubAdmin,
    deleteClubAdmin,
    searchClubAdmins,
    fetchTitres,
    createTitre,
    updateTitre,
    deleteTitre,
    clubAdminExists,
  }
})
