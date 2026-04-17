<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import { useUploadMedia } from '@/services/useUploadMedia';
import { usePostStore } from '@/stores/PostStore';

const { uploadMedia, isUploading, uploadProgress } = useUploadMedia();
const postStore = usePostStore();

const postContent = ref('');
const selectedFiles = ref([]);
const previews = ref([]);
const error = ref('');
const success = ref('');
const isPublishing = ref(false);

const MAX_FILE_SIZE = 100 * 1024 * 1024; // 100MB
const MAX_FILES = 10;
const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
const ALLOWED_VIDEO_TYPES = ['video/mp4', 'video/webm', 'video/quicktime'];

const canAddMoreFiles = computed(() => selectedFiles.value.length < MAX_FILES);

const clearMessages = () => {
  error.value = '';
  success.value = '';
};

const showError = (message) => {
  error.value = message;
  setTimeout(() => (error.value = ''), 5000);
};

const showSuccess = (message) => {
  success.value = message;
  setTimeout(() => (success.value = ''), 3000);
};

const validateFile = (file) => {
  if (file.size > MAX_FILE_SIZE) {
    showError(`Le fichier "${file.name}" dépasse la limite de 100MB`);
    return false;
  }

  const isImage = ALLOWED_IMAGE_TYPES.includes(file.type);
  const isVideo = ALLOWED_VIDEO_TYPES.includes(file.type);

  if (!isImage && !isVideo) {
    showError(`Le format "${file.type}" n'est pas autorisé. Utilisez JPG, PNG, WebP, GIF ou MP4`);
    return false;
  }

  const isDuplicate = selectedFiles.value.some(
    f => f.name === file.name && f.size === file.size
  );

  if (isDuplicate) {
    showError(`Le fichier "${file.name}" est déjà sélectionné`);
    return false;
  }

  return true;
};

const handleFileSelect = (e) => {
  clearMessages();

  if (!canAddMoreFiles.value) {
    showError(`Limite atteinte : maximum ${MAX_FILES} fichiers autorisés`);
    return;
  }

  const files = Array.from(e.target.files);
  let validFilesCount = 0;

  files.forEach(file => {
    if (selectedFiles.value.length >= MAX_FILES) {
      showError(`Limite atteinte : maximum ${MAX_FILES} fichiers autorisés`);
      return;
    }

    if (!validateFile(file)) {
      return;
    }

    selectedFiles.value.push(file);
    validFilesCount++;

    const reader = new FileReader();

    reader.onload = (event) => {
      previews.value.push({
        url: event.target.result,
        type: file.type.startsWith('video') ? 'VIDEO' : 'IMAGE',
        file: file,
        name: file.name,
        size: (file.size / 1024 / 1024).toFixed(2),
        id: Date.now() + Math.random() // ID unique pour key
      });
    };

    reader.onerror = () => {
      showError(`Erreur de lecture du fichier "${file.name}"`);
      const idx = selectedFiles.value.indexOf(file);
      if (idx > -1) selectedFiles.value.splice(idx, 1);
    };

    reader.readAsDataURL(file);
  });

  if (validFilesCount > 0) {
    showSuccess(`${validFilesCount} fichier(s) ajouté(s)`);
  }

  e.target.value = '';
};

const removeFile = (id) => {
  const previewIndex = previews.value.findIndex(p => p.id === id);
  const fileIndex = selectedFiles.value.indexOf(previews.value[previewIndex].file);

  if (previewIndex > -1) {
    const removedName = previews.value[previewIndex].name;
    previews.value.splice(previewIndex, 1);
    showSuccess(`Fichier "${removedName}" supprimé`);
  }

  if (fileIndex > -1) {
    selectedFiles.value.splice(fileIndex, 1);
  }
};

const handlePublish = async () => {
  clearMessages();

  if (!postContent.value.trim() && selectedFiles.value.length === 0) {
    showError('Veuillez ajouter du texte ou des fichiers');
    return;
  }

  if (postContent.value.trim().length > 5000) {
    showError('Le texte ne doit pas dépasser 5000 caractères');
    return;
  }

  try {
    isPublishing.value = true;

    let uploadedMedia = [];
    let media = [];

    if (selectedFiles.value.length > 0) {
      uploadedMedia = await uploadMedia(selectedFiles.value);
    }

    media = uploadedMedia.map(item => ({
      url: item.url,
      mediaType: item.type.toUpperCase()
    }));

    console.log('Contenu du post:', postContent.value);
    console.log('Media formaté pour le backend:', media);

    await postStore.createPost(postContent.value.trim(), media);

    showSuccess('Post publié avec succès !');

    postContent.value = '';
    selectedFiles.value = [];
    previews.value = [];

  } catch (error) {
    console.error('Erreur publication:', error);
    const errorMessage = error.response?.data?.message || error.message || 'Erreur lors de la publication';
    showError(`Erreur : ${errorMessage}`);
  } finally {
    isPublishing.value = false;
  }
};
</script>

<template>
  <div class="bg-white rounded-3xl border border-gray-200 p-6 shadow-sm mb-6">
    <!-- Messages -->
    <div v-if="error" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm font-medium flex items-center gap-2">
      <i class="fa-solid fa-circle-exclamation"></i>
      {{ error }}
    </div>

    <div v-if="success" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium flex items-center gap-2">
      <i class="fa-solid fa-check-circle"></i>
      {{ success }}
    </div>

    <!-- Textarea -->
    <div class="flex gap-4">
      <img src="https://ui-avatars.com/api/?name=Abderrazak&bg=064E3B&color=fff"
           class="h-12 w-12 rounded-2xl object-cover flex-shrink-0" alt="Avatar">
      <div class="w-full">
        <textarea
          v-model="postContent"
          placeholder="Partagez vos performances ou un nouveau contrat..."
          class="w-full border border-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-transparent resize-none text-md bg-gray-50 rounded-2xl p-4 min-h-[100px] placeholder:text-gray-400 font-medium"
          maxlength="5000"
        ></textarea>
        <div class="text-right text-xs text-gray-400 mt-1">
          {{ postContent.length }}/5000
        </div>
      </div>
    </div>

    <!-- Previews -->
    <div v-if="previews.length > 0" class="mt-4">
      <div class="flex gap-3 overflow-x-auto pb-2">
        <div v-for="preview in previews" :key="preview.id" class="relative shrink-0 group">
          <!-- IMAGE -->
          <img
            v-if="preview.type === 'IMAGE'"
            :src="preview.url"
            :alt="preview.name"
            class="h-24 w-24 rounded-2xl object-cover border border-gray-300 shadow-sm"
          />

          <!-- VIDEO -->
          <div v-else class="relative h-24 w-24 rounded-2xl overflow-hidden border border-gray-300 shadow-sm bg-black">
            <video
              :src="preview.url"
              class="h-full w-full object-cover"
              @loadedmetadata="e => e.target.play()"
            ></video>
            <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30">
              <i class="fa-solid fa-play text-white text-xl"></i>
            </div>
          </div>

          <!-- Overlay Info -->
          <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 rounded-2xl transition flex flex-col items-center justify-center opacity-0 group-hover:opacity-100">
            <span class="text-white text-[10px] font-semibold text-center px-1">
              {{ preview.name.substring(0, 15) }}{{ preview.name.length > 15 ? '...' : '' }}
            </span>
            <span class="text-gray-200 text-[9px]">{{ preview.size }}MB</span>
          </div>

          <!-- Delete Button -->
          <button
            @click="removeFile(preview.id)"
            type="button"
            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white w-6 h-6 rounded-full text-xs flex items-center justify-center shadow-lg transition transform hover:scale-110"
          >
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Upload Progress -->
    <div v-if="isUploading" class="mt-4">
      <div class="flex justify-between text-[10px] font-black uppercase text-gray-400 mb-1">
        <span>Téléchargement vers Cloudinary...</span>
        <span>{{ uploadProgress }}%</span>
      </div>
      <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
        <div class="bg-emerald-600 h-full transition-all duration-300" :style="{ width: uploadProgress + '%' }"></div>
      </div>
    </div>

    <!-- Footer -->
    <div class="flex justify-between items-center mt-6 pt-5 border-t border-gray-50">
      <div class="flex gap-2 items-center">
        <input
          type="file"
          id="photo-up"
          hidden
          @change="handleFileSelect"
          accept="image/*"
          multiple
          :disabled="!canAddMoreFiles"
        >
        <input
          type="file"
          id="video-up"
          hidden
          @change="handleFileSelect"
          accept="video/*"
          multiple
          :disabled="!canAddMoreFiles"
        >

        <label
          for="photo-up"
          class="cursor-pointer flex items-center gap-2 text-[11px] font-black text-gray-500 hover:bg-emerald-50 hover:text-emerald-700 px-4 py-2.5 rounded-xl transition uppercase tracking-wider"
          :class="{ 'opacity-50 cursor-not-allowed': !canAddMoreFiles }"
        >
          <i class="fa-solid fa-image text-emerald-600 text-sm"></i> Photo
        </label>

        <label
          for="video-up"
          class="cursor-pointer flex items-center gap-2 text-[11px] font-black text-gray-500 hover:bg-red-50 hover:text-red-700 px-4 py-2.5 rounded-xl transition uppercase tracking-wider"
          :class="{ 'opacity-50 cursor-not-allowed': !canAddMoreFiles }"
        >
          <i class="fa-solid fa-video text-red-500 text-sm"></i> Vidéo
        </label>

        <span v-if="selectedFiles.length > 0" class="text-[11px] text-gray-400 font-medium ml-2 py-2.5">
          {{ selectedFiles.length }}/{{ MAX_FILES }} fichiers
        </span>
      </div>

      <button
        @click="handlePublish"
        type="button"
        :disabled="isUploading || isPublishing || (!postContent.trim() && selectedFiles.length === 0)"
        class="bg-[#064E3B] hover:bg-[#043a2c] disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-8 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all shadow-md active:scale-95"
      >
        <span v-if="isUploading || isPublishing">
          <i class="fa-solid fa-spinner animate-spin mr-2"></i>Patientez...
        </span>
        <span v-else>Publier</span>
      </button>
    </div>
  </div>
</template> 
