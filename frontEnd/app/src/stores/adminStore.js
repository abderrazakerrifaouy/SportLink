import { defineStore } from 'pinia'
import api from '@/api/api'

const normalizeId = (value, resourceLabel) => {
  const normalizedId = Number(value)

  if (!Number.isInteger(normalizedId) || normalizedId <= 0) {
    throw new Error(`Invalid ${resourceLabel} ID`)
  }

  return normalizedId
}

const getErrorMessage = (error, fallbackMessage) => error?.response?.data?.message || error?.message || fallbackMessage

export const useAdminStore = defineStore('admin', {
  state: () => ({
    statistics: {
      totalUsers: 0,
      totalPosts: 0,
      totalComments: 0,
      totalPlayers: 0,
      totalClubAdmins: 0,
      pendingReports: 0,
    },
    users: [],
    posts: [],
    comments: [],
    reports: [],
    isLoading: false,
    error: null,
  }),

  getters: {
    isAdmin: () => {
      const user = JSON.parse(localStorage.getItem('user') || '{}')
      return user?.role === 'ADMIN'
    },
  },

  actions: {
    async fetchStatistics() {
      this.isLoading = true
      this.error = null
      try {
        const response = await api.get('/admin/statistics')
        this.statistics = response.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch statistics'
        throw error
      } finally {
        this.isLoading = false
      }
    },

    async fetchUsers(page = 1, perPage = 15) {
      this.isLoading = true
      this.error = null
      try {
        const response = await api.get(`/admin/users`, { params: { page, per_page: perPage } })
        this.users = response.data.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch users'
        throw error
      } finally {
        this.isLoading = false
      }
    },

    async searchUsers(query, page = 1, perPage = 15) {
      this.isLoading = true
      this.error = null
      try {
        const response = await api.get(`/admin/users/search`, { params: { q: query, page, per_page: perPage } })
        this.users = response.data.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to search users'
        throw error
      } finally {
        this.isLoading = false
      }
    },

    async deleteUser(userId) {
      this.isLoading = true
      this.error = null
      try {
        const normalizedUserId = normalizeId(userId, 'user')
        await api.delete(`/admin/users/${normalizedUserId}`)
        this.users = this.users.filter(u => String(u.id) !== String(normalizedUserId))
        this.statistics.totalUsers = Math.max(0, this.statistics.totalUsers - 1)
        return true
      } catch (error) {
        this.error = getErrorMessage(error, 'Failed to delete user')
        throw error
      } finally {
        this.isLoading = false
      }
    },

    async fetchPosts(page = 1, perPage = 15) {
      this.isLoading = true
      this.error = null
      try {
        const response = await api.get(`/admin/posts`, { params: { page, per_page: perPage } })
        this.posts = response.data.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch posts'
        throw error
      } finally {
        this.isLoading = false
      }
    },

    async searchPosts(query, page = 1, perPage = 15) {
      this.isLoading = true
      this.error = null
      try {
        const response = await api.get(`/admin/posts/search`, { params: { q: query, page, per_page: perPage } })
        this.posts = response.data.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to search posts'
        throw error
      } finally {
        this.isLoading = false
      }
    },

    async deletePost(postId) {
      this.isLoading = true
      this.error = null
      try {
        const normalizedPostId = normalizeId(postId, 'post')
        await api.delete(`/admin/posts/${normalizedPostId}`)
        this.posts = this.posts.filter(p => String(p.id) !== String(normalizedPostId))
        this.statistics.totalPosts = Math.max(0, this.statistics.totalPosts - 1)
        return true
      } catch (error) {
        this.error = getErrorMessage(error, 'Failed to delete post')
        throw error
      } finally {
        this.isLoading = false
      }
    },

    async fetchComments(page = 1, perPage = 15) {
      this.isLoading = true
      this.error = null
      try {
        const response = await api.get(`/admin/comments`, { params: { page, per_page: perPage } })
        this.comments = response.data.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch comments'
        throw error
      } finally {
        this.isLoading = false
      }
    },

    async deleteComment(commentId) {
      this.isLoading = true
      this.error = null
      try {
        const normalizedCommentId = normalizeId(commentId, 'comment')
        await api.delete(`/admin/comments/${normalizedCommentId}`)
        this.comments = this.comments.filter(c => String(c.id) !== String(normalizedCommentId))
        this.statistics.totalComments = Math.max(0, this.statistics.totalComments - 1)
        return true
      } catch (error) {
        this.error = getErrorMessage(error, 'Failed to delete comment')
        throw error
      } finally {
        this.isLoading = false
      }
    },

    async fetchReports(status = null, query = null, page = 1, perPage = 15) {
      this.isLoading = true
      this.error = null
      try {
        const params = { page, per_page: perPage }
        if (status) params.status = status
        if (query) params.q = query
        const response = await api.get(`/admin/reports`, { params })
        this.reports = response.data.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch reports'
        throw error
      } finally {
        this.isLoading = false
      }
    },

    async resolveReport(reportId, status) {
      this.error = null
      try {
        const normalizedReportId = normalizeId(reportId, 'report')
        await api.patch(`/admin/reports/${normalizedReportId}/resolve`, { status })
        const report = this.reports.find(r => String(r.id) === String(normalizedReportId))
        if (report) {
          const wasPending = report.status === 'pending'
          report.status = status
          if (wasPending) {
            this.statistics.pendingReports = Math.max(0, this.statistics.pendingReports - 1)
          }
        }
        return true
      } catch (error) {
        this.error = getErrorMessage(error, 'Failed to resolve report')
        throw error
      }
    },

    async deleteReport(reportId) {
      this.error = null
      try {
        const normalizedReportId = normalizeId(reportId, 'report')
        const report = this.reports.find(r => String(r.id) === String(normalizedReportId))
        await api.delete(`/admin/reports/${normalizedReportId}`)
        this.reports = this.reports.filter(r => String(r.id) !== String(normalizedReportId))
        if (report?.status === 'pending') {
          this.statistics.pendingReports = Math.max(0, this.statistics.pendingReports - 1)
        }
        return true
      } catch (error) {
        this.error = getErrorMessage(error, 'Failed to delete report')
        throw error
      }
    },
  },
})
