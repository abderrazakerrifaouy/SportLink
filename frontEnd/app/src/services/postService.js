import apiClient from '@/api/api'

export default {
  getAllPosts() {
    return apiClient.get('/posts')
  },

  getPostById(id) {
    return apiClient.get(`/posts/${id}`)
  },

  createPost(data) {
    if (data.media && data.media.length > 0) {
      const formData = new FormData()
      formData.append('content', data.content)
      if (data.media && data.media.length > 0) {
        data.media.forEach((file, index) => {
          formData.append(`media[${index}][url]`, file.url)
          formData.append(`media[${index}][mediaType]`, file.mediaType)
        })
      }
      return apiClient.post('/posts', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }
    return apiClient.post('/posts', { content: data.content })
  },

  updatePost(id, data) {
    if (data.media && data.media.length > 0) {
      const formData = new FormData()
      formData.append('content', data.content)
      data.media.forEach((file, index) => {
        formData.append(`media[${index}][url]`, file.url)
        formData.append(`media[${index}][mediaType]`, file.mediaType)
      })
      return apiClient.put(`/posts/${id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }
    return apiClient.put(`/posts/${id}`, { content: data.content })
  },

  deletePost(id) {
    return apiClient.delete(`/posts/${id}`)
  },

  getPostsByUserId(userId) {
    return apiClient.get(`/posts/user/${userId}`)
  },
}

