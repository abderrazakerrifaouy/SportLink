import apiClient from '@/api/api'

export default {
  createReport(data) {
    // data: { reportable_type, reportable_id, reason }
    return apiClient.post('/reports', data)
  },

  getReport(id) {
    return apiClient.get(`/reports/${id}`)
  },

  getMyReports() {
    return apiClient.get('/reports/my-reports')
  }
}
