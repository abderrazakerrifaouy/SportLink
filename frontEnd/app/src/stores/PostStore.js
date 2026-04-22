import { ref } from 'vue'
import { defineStore } from 'pinia'
import postService from '@/services/postService'
import commentService from '@/services/commentService'
import reactionService from '@/services/reactionService'
import { useAuthStore } from './AuthStore'

export const usePostStore = defineStore('post', () => {
  const posts = ref([])
  const currentPost = ref(null)
  const loading = ref(false)
  const postComments = ref({})
  const postReactions = ref({})

  const authStore = useAuthStore()

  const REACTION_TYPES = ['LIKE', 'LOVE', 'HAHA', 'WOW', 'GRR', 'SAD', 'DISLIKE']

  async function fetchPosts() {
    loading.value = true
    try {
      const response = await postService.getAllPosts()
      posts.value = response.data

      response.data.forEach(post => {
        if (post.reactions && post.reactions.length > 0) {
          postReactions.value[post.id] = post.reactions
        }
      })

      return response.data
    } catch (error) {
      console.error('Failed to fetch posts:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  async function fetchPostById(id) {
    try {
      const response = await postService.getPostById(id)
      currentPost.value = response.data
      return response.data
    } catch (error) {
      console.error('Failed to fetch post:', error)
      throw error
    }
  }

  async function fetchPostsByUser(userId) {
    try {
      const response = await postService.getPostsByUserId(userId)
      return response.data || []
    } catch (error) {
      console.error('Failed to fetch user posts:', error)
      return []
    }
  }

  async function createPost(content, media = null) {
    try {
      const response = await postService.createPost({ content, media })
      posts.value.unshift(response.data)
      return response.data
    } catch (error) {
      console.error('Failed to create post:', error)
      throw error
    }
  }

  async function updatePost(id, content, media) {
    try {
      const response = await postService.updatePost(id, { content, media })
      const index = posts.value.findIndex((p) => p.id === id)
      if (index !== -1) posts.value[index] = response.data
      if (currentPost.value?.id === id) currentPost.value = response.data
      return response.data
    } catch (error) {
      console.error('Failed to update post:', error)
      throw error
    }
  }

  async function deletePost(id) {
    try {
      await postService.deletePost(id)
      posts.value = posts.value.filter((p) => p.id !== id)
      if (currentPost.value?.id === id) currentPost.value = null
    } catch (error) {
      console.error('Failed to delete post:', error)
      throw error
    }
  }

  async function fetchComments(postId) {
    try {
      const response = await commentService.getCommentsByPostId(postId)
      postComments.value[postId] = response.data
      return response.data
    } catch (error) {
      console.error('Failed to fetch comments:', error)
      throw error
    }
  }

  async function addComment(postId, content) {
    try {
      const response = await commentService.addComment(postId, content)
      if (!postComments.value[postId]) {
        postComments.value[postId] = []
      }
      postComments.value[postId].push(response.data)
      const postIndex = posts.value.findIndex((p) => p.id === postId)
      if (postIndex !== -1) {
        posts.value[postIndex].comments_count = (posts.value[postIndex].comments_count || 0) + 1
      }
      return response.data
    } catch (error) {
      console.error('Failed to add comment:', error)
      throw error
    }
  }

  async function addReaction(postId, type) {
    if (!REACTION_TYPES.includes(type)) return
    try {
      const currentUserId = authStore.user?.id
      if (!currentUserId) return

      const existingReaction = postReactions.value[postId]?.find(
        (r) => r.user_id === currentUserId && r.type === type
      )
      if (existingReaction) {
        await deleteReaction(postId, existingReaction.id)
        return
      }

      const otherReactions = postReactions.value[postId]?.filter(
        (r) => r.user_id === currentUserId
      ) || []
      for (const r of otherReactions) {
        await deleteReaction(postId, r.id)
      }

      const response = await reactionService.createReaction({
        type,
        user_id: currentUserId,
        reactable_id: postId,
        reactable_type: 'App\\Models\\Post',
      })

      if (!postReactions.value[postId]) {
        postReactions.value[postId] = []
      }
      postReactions.value[postId].push(response.data)

      await updatePostReactionsSummary(postId)
      return response.data
    } catch (error) {
      console.error('Failed to add reaction:', error)
      throw error
    }
  }

  async function deleteReaction(postId, reactionId) {
    try {
      await reactionService.deleteReaction(reactionId)
      if (postReactions.value[postId]) {
        postReactions.value[postId] = postReactions.value[postId].filter(
          (r) => r.id !== reactionId
        )
      }
      await updatePostReactionsSummary(postId)
    } catch (error) {
      console.error('Failed to delete reaction:', error)
      throw error
    }
  }

  async function toggleReaction(postId, type) {
    const currentUserId = authStore.user?.id
    if (!currentUserId) return

    const existingReaction = postReactions.value[postId]?.find(
      (r) => r.user_id === currentUserId && r.type === type
    )
    if (existingReaction) {
      await deleteReaction(postId, existingReaction.id)
    } else {
      await addReaction(postId, type)
    }
  }

  async function updatePostReactionsSummary(postId) {
    const postIndex = posts.value.findIndex((p) => p.id === postId)
    if (postIndex === -1) return

    const reactions = postReactions.value[postId] || []
    const summary = {
      total: reactions.length,
      likes: reactions.filter((r) => r.type === 'LIKE').length,
      dislikes: reactions.filter((r) => r.type === 'DISLIKE').length,
      loves: reactions.filter((r) => r.type === 'LOVE').length,
      wows: reactions.filter((r) => r.type === 'WOW').length,
      hahas: reactions.filter((r) => r.type === 'HAHA').length,
      grrs: reactions.filter((r) => r.type === 'GRR').length,
      sads: reactions.filter((r) => r.type === 'SAD').length,
    }
    posts.value[postIndex].reactions_summary = summary
  }

  function getUserReactions(postId) {
    const currentUserId = authStore.user?.id
    if (!currentUserId || !postReactions.value[postId]) return []
    return postReactions.value[postId].filter((r) => r.user_id === currentUserId)
  }

  return {
    posts,
    currentPost,
    loading,
    postComments,
    postReactions,
    REACTION_TYPES,
    fetchPosts,
    fetchPostById,
    fetchPostsByUser,
    createPost,
    updatePost,
    deletePost,
    fetchComments,
    addComment,
    addReaction,
    deleteReaction,
    toggleReaction,
    getUserReactions,
  }
})
