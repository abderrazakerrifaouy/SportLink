import { ref } from 'vue'
import { defineStore } from 'pinia'
import commentService from '@/services/commentService'
import reactionService from '@/services/reactionService'
import { usePostStore } from './PostStore'
import { useAuthStore } from './AuthStore'

export const useCommentStore = defineStore('comment', () => {
  const commentsByPost = ref({})
  const postStore = usePostStore()
  const authStore = useAuthStore()

  const getComments = (postId) => commentsByPost.value[postId] || []

  const normalizeNode = (node, type = 'comment') => {
    if (!node) return null

    const user = node.user || node.author || {}
    const replies = Array.isArray(node.replies)
      ? node.replies.map((reply) => normalizeNode(reply, 'reply')).filter(Boolean)
      : []

    return {
      ...node,
      node_type: type,
      user,
      replies,
      reactions_summary: node.reactions_summary || {
        total: node.reactions_count || 0,
        likes: 0,
        loves: 0,
        hahas: 0,
        wows: 0,
        sads: 0,
        grrs: 0,
        dislikes: 0
      },
      user_reaction: node.user_reaction || null
    }
  }

  const updateNodeById = (items, targetId, updater) => {
    for (const item of items) {
      if (item.id === targetId) {
        updater(item)
        return true
      }

      if (item.replies?.length && updateNodeById(item.replies, targetId, updater)) {
        return true
      }
    }

    return false
  }

  async function fetchComments(postId) {
    try {
      const response = await commentService.getCommentsByPostId(postId)
      commentsByPost.value[postId] = Array.isArray(response.data)
        ? response.data.map((comment) => normalizeNode(comment, 'comment')).filter(Boolean)
        : []
    } catch (error) {
      console.error('Fetch comments failed:', error)
    }
  }

  async function addComment(postId, content) {
    try {
      const response = await commentService.addComment(postId, content)
      const normalized = normalizeNode(response.data, 'comment')

      if (!commentsByPost.value[postId]) commentsByPost.value[postId] = []
      commentsByPost.value[postId].unshift(normalized)

      const post = postStore.posts.find(p => p.id === postId)
      if (post) post.comments_count++

      return normalized
    } catch (error) {
      console.error('Add comment failed:', error)
      throw error
    }
  }

  async function addReply(postId, commentId, content) {
    try {
      const response = await commentService.addReply(commentId, content)
      const normalizedReply = normalizeNode(response.data, 'reply')

      const comments = commentsByPost.value[postId] || []
      updateNodeById(comments, commentId, (node) => {
        if (!node.replies) node.replies = []
        node.replies.unshift(normalizedReply)
      })

      const post = postStore.posts.find(p => p.id === postId)
      if (post) post.comments_count++

      return normalizedReply
    } catch (error) {
      console.error('Add reply failed:', error)
      throw error
    }
  }

  async function toggleReaction(postId, targetId, type, targetType = 'comment') {
    if (!authStore.user?.id || !type) return

    const reactableType = targetType === 'reply' ? 'App\\Models\\Reply' : 'App\\Models\\Comment'

    try {
      await reactionService.createReaction({
        type,
        reactable_id: targetId,
        reactable_type: reactableType
      })

      await fetchComments(postId)
    } catch (error) {
      if (error?.response?.status === 422) {
        await fetchComments(postId)
        return
      }

      console.error('Comment reaction failed:', error)
      throw error
    }
  }

  return {
    commentsByPost,
    getComments,
    fetchComments,
    addComment,
    addReply,
    toggleReaction
  }
})
