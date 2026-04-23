import { ref } from 'vue'
import axios from 'axios'

export function useUploadMedia() {
  const isUploading = ref(false)
  const uploadProgress = ref(0)

  const CLOUD_NAME = 'dr7sf8mgw'
  const UPLOAD_PRESET = 'sportlink_preset'

  const uploadFile = async (file) => {
    const formData = new FormData()
    formData.append('file', file)
    formData.append('upload_preset', UPLOAD_PRESET)

    const url = `https://api.cloudinary.com/v1_1/${CLOUD_NAME}/auto/upload`

    const res = await axios.post(url, formData, {
      onUploadProgress: (p) => {
        uploadProgress.value = Math.round((p.loaded * 100) / p.total)
      }
    })

    return {
      url: res.data.secure_url,
      type: res.data.resource_type === 'video' ? 'VIDEO' : 'IMAGE'
    }
  }

  const uploadMedia = async (files) => {
    isUploading.value = true
    try {
      return await Promise.all(files.map(f => uploadFile(f)))
    } finally {
      isUploading.value = false
    }
  }

  return { uploadMedia, isUploading, uploadProgress }
}
