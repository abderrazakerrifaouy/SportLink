import { ref } from 'vue'
import { defineStore } from 'pinia'
import joueurService from '@/services/joueurService'

export const useJoueurStore = defineStore('joueur', () => {
  const joueurs = ref([])
  const currentJoueur = ref(null)
  const experiences = ref([])

  const fetchJoueurs = async () => {
    const response = await joueurService.getJoueurs()
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
    fetchJoueurs,
    fetchJoueur,
    createJoueur,
    fetchExperiences,
    createExperience,
    updateExperience,
    deleteExperience,
  }
})
