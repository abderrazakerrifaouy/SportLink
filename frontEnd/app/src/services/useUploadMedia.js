import { ref } from 'vue';
import axios from 'axios';

export function useUploadMedia() {
  const isUploading = ref(false);
  const uploadProgress = ref(0);
  const uploadError = ref(null);

  const CLOUD_NAME = 'dr7sf8mgw';
  const UPLOAD_PRESET = 'sportlink_preset';

  const uploadFile = async (file) => {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('upload_preset', UPLOAD_PRESET);

    const url = `https://api.cloudinary.com/v1_1/${CLOUD_NAME}/auto/upload`;

    try {
      const res = await axios.post(url, formData, {
        onUploadProgress: (progressEvent) => {
          uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
        }
      });

      return {
        publicId: res.data.public_id, // L-SDK k-i-bghi l-Public ID ktar mn l-URL
        url: res.data.secure_url,
        type: res.data.resource_type === 'video' ? 'VIDEO' : 'IMAGE'
      };
    } catch (err) {
      uploadError.value = "Erreur d'upload";
      throw err;
    }
  };

  const uploadMedia = async (files) => {
    isUploading.value = true;
    uploadProgress.value = 0;
    try {
      const results = await Promise.all(files.map(f => uploadFile(f)));
      return results;
    } finally {
      isUploading.value = false;
    }
  };

  return { uploadMedia, isUploading, uploadProgress, uploadError };
}
