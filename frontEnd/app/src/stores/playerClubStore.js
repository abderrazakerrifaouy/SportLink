import { ref } from 'vue'
import { defineStore } from 'pinia'
import clubRequestService from '@/services/clubRequestService'
import clubAdminService from '@/services/cloubAdminService'

export const usePlayerClubStore = defineStore('playerClub', () => {
  const currentClubRequest = ref(null)
  const currentClubDetails = ref(null)
  const loading = ref(false)
  const leaving = ref(false)
  const error = ref(null)

  const fetchMyClub = async () => {
    loading.value = true
    error.value = null

    try {
      const response = await clubRequestService.getMyClub()
      currentClubRequest.value = response.data

      const clubId = response.data?.club?.id
      if (clubId) {
        const detailsResponse = await clubAdminService.getClubAdminById(clubId)
        currentClubDetails.value = detailsResponse.data
      } else {
        currentClubDetails.value = null
      }

      return currentClubDetails.value
    } catch (err) {
      if (err?.response?.status === 404) {
        currentClubRequest.value = null
        currentClubDetails.value = null
        return null
      }

      error.value = err?.response?.data?.message || 'Failed to load your club.'
      throw err
    } finally {
      loading.value = false
    }
  }

  const leaveClub = async () => {
    leaving.value = true
    error.value = null

    try {
      const response = await clubRequestService.leaveMyClub()
      currentClubRequest.value = null
      currentClubDetails.value = null
      return response.data
    } catch (err) {
      error.value = err?.response?.data?.message || 'Failed to leave club.'
      throw err
    } finally {
      leaving.value = false
    }
  }

  return {
    currentClubRequest,
    currentClubDetails,
    loading,
    leaving,
    error,
    fetchMyClub,
    leaveClub,
  }
})
