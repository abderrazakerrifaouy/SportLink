import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import joueurService from '@/services/joueurService'

export const useJoueurStore = defineStore('joueur', () => {
  const joueurs = ref({ data: [], total: 0 })
  const currentJoueur = ref(null)
  const experiences = ref([])
  const searchQuery = ref('')

  const filteredJoueurs = computed(() => {
    const allPlayers = joueurs.value.data || []
    if (!searchQuery.value) return allPlayers

    const query = searchQuery.value.toLowerCase()
    return allPlayers.filter(player => {
      const userName = player.user?.name?.toLowerCase() || ''
      const userEmail = player.user?.email?.toLowerCase() || ''
      const latestExp = player.experiences?.[0]
      const clubName = latestExp?.nomClub?.toLowerCase() || ''
      const category = latestExp?.categoryType?.toLowerCase() || ''
      const place = latestExp?.place?.toLowerCase() || ''

      return userName.includes(query) ||
             userEmail.includes(query) ||
             clubName.includes(query) ||
             category.includes(query) ||
             place.includes(query)
    })
  })

  const setSearchQuery = (query) => {
    searchQuery.value = query
  }

  const fetchJoueurs = async () => {
    const response = await joueurService.getAllJoueurs()
    joueurs.value = response.data
    return response.data
  }

  const fetchJoueur = async (id) => {
    const response = await joueurService.getJoueur(id)
    currentJoueur.value = response.data
    return response.data
  }

  const createJoueur = async (data) => {
    const response = await joueurService.createJoueur(data)
    currentJoueur.value = response.data
    return response.data
  }

  const fetchExperiences = async (id) => {
    const response = await joueurService.getExperiencesByJoueurId(id)
    experiences.value = response.data
    return response.data
  }

  const createExperience = async (data) => {
    const response = await joueurService.createExperience(data)
    experiences.value.push(response.data)
    return response.data
  }

  const updateExperience = async (id, data) => {
    const response = await joueurService.updateExperience(id, data)
    const index = experiences.value.findIndex((e) => e.id === id)
    if (index !== -1) experiences.value[index] = response.data
    return response.data
  }

  const deleteExperience = async (id) => {
    await joueurService.deleteExperience(id)
    experiences.value = experiences.value.filter((e) => e.id !== id)
  }

  return {
    joueurs,
    currentJoueur,
    experiences,
    searchQuery,
    filteredJoueurs,
    setSearchQuery,
    fetchJoueurs,
    fetchJoueur,
    createJoueur,
    fetchExperiences,
    createExperience,
    updateExperience,
    deleteExperience,
  }
})
