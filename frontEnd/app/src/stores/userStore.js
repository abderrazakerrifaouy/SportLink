import { ref } from 'vue'
import { defineStore } from 'pinia'
import userService from '@/services/userService'

export const useUserStore = defineStore('user', () => {
  const users = ref([])
  const currentProfile = ref(null)
  const searchResults = ref([])
  const followers = ref([])
  const following = ref([])
  const conversations = ref([])
  const messages = ref([])

  const fetchUsers = async () => {
    const response = await userService.getUsers()
    users.value = response.data
    return response.data
  }

  const fetchUser = async (id) => {
    const response = await userService.getUser(id)
    currentProfile.value = response.data
    return response.data
  }

  const updateUser = async (id, data) => {
    const response = await userService.updateUser(id, data)
    return response.data
  }

  const searchUsers = async (name) => {
    const response = await userService.searchUsers(name)
    searchResults.value = response.data
    return response.data
  }

  const follow = async (id) => {
    await userService.follow(id)
  }

  const unfollow = async (id) => {
    await userService.unfollow(id)
  }

  const isFollowing = async (id) => {
    const response = await userService.isFollowing(id)
    return response.data
  }

  const fetchFollowers = async (id) => {
    const response = await userService.getFollowers(id)
    followers.value = response.data
    return response.data
  }

  const fetchFollowing = async (id) => {
    const response = await userService.getFollowing(id)
    following.value = response.data
    return response.data
  }

  const fetchConversations = async () => {
    const response = await userService.getConversations()
    conversations.value = response.data
    return response.data
  }

  const fetchMessages = async (userId1, userId2) => {
    const response = await userService.getMessages(userId1, userId2)
    messages.value = response.data
    return response.data
  }

  const sendMessage = async (receiverId, content) => {
    const response = await userService.sendMessage(receiverId, content)
    messages.value.push(response.data)
    return response.data
  }

  const deleteMessage = async (messageId) => {
    await userService.deleteMessage(messageId)
    messages.value = messages.value.filter((m) => m.id !== messageId)
  }

  const updateMessage = async (messageId, content) => {
    const response = await userService.updateMessage(messageId, content)
    const index = messages.value.findIndex((m) => m.id === messageId)
    if (index !== -1) messages.value[index] = response.data
    return response.data
  }

  return {
    users,
    currentProfile,
    searchResults,
    followers,
    following,
    conversations,
    messages,
    fetchUsers,
    fetchUser,
    updateUser,
    searchUsers,
    follow,
    unfollow,
    isFollowing,
    fetchFollowers,
    fetchFollowing,
    fetchConversations,
    fetchMessages,
    sendMessage,
    deleteMessage,
    updateMessage,
  }
})
