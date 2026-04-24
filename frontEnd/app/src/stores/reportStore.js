import { ref } from 'vue'
import { defineStore } from 'pinia'
import reportService from '@/services/reportService'

export const useReportStore = defineStore('report', () => {
  const reports = ref([])
  const loading = ref(false)
  const error = ref(null)

  // Fetch user's reports
  async function fetchMyReports() {
    loading.value = true
    error.value = null
    try {
      const response = await reportService.getMyReports()
      reports.value = response.data
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch reports'
      console.error('Failed to fetch reports:', err)
      throw error.value
    } finally {
      loading.value = false
    }
  }

  // Create a new report
  async function createReport(reportableType, reportableId, reason) {
    loading.value = true
    error.value = null
    try {
      const response = await reportService.createReport({
        reportable_type: reportableType,
        reportable_id: reportableId,
        reason: reason
      })

      // Add new report to the list
      reports.value.unshift(response.data)

      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create report'
      console.error('Failed to create report:', err)
      throw error.value
    } finally {
      loading.value = false
    }
  }

  // Get report details
  async function getReport(id) {
    loading.value = true
    error.value = null
    try {
      const response = await reportService.getReport(id)
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch report'
      console.error('Failed to fetch report:', err)
      throw error.value
    } finally {
      loading.value = false
    }
  }

  // Clear error
  function clearError() {
    error.value = null
  }

  return {
    reports,
    loading,
    error,
    fetchMyReports,
    createReport,
    getReport,
    clearError
  }
})
