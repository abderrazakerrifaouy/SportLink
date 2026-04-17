<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useUploadMedia } from '@/services/useUploadMedia';
import { usePostStore } from '@/stores/PostStore';

const { uploadMedia, isUploading, uploadProgress } = useUploadMedia();
const postStore = usePostStore();

const postContent = ref('');
const selectedFiles = ref([]);
const previews = ref([]);

// Khtar files (Images/Videos)
const handleFileSelect = (e) => {
  const files = Array.from(e.target.files);
  selectedFiles.value.push(...files);

  // Creer preview bach t-ban l-user
  files.forEach(file => {
    const reader = new FileReader();
    reader.onload = (e) => {
      previews.value.push({
        url: e.target.result,
        type: file.type.startsWith('video') ? 'VIDEO' : 'IMAGE',
        file: file
      });
    };
    reader.readAsDataURL(file);
  });
};

const removeFile = (index) => {
  selectedFiles.value.splice(index, 1);
  previews.value.splice(index, 1);
};

const handlePublish = async () => {
  if (!postContent.value && selectedFiles.value.length === 0) return;

  try {
    let uploadedMedia = [];
    let media = [];
    if (selectedFiles.value.length > 0) {
      uploadedMedia = await uploadMedia(selectedFiles.value);
    }

    console.log('Contenu du post:', postContent.value);
    media = uploadedMedia.map(item => ({
      url: item.url,
      mediaType: item.type.toUpperCase()
    }));
    console.log('Media formaté pour le backend:', media);
    // console.log('Media formaté pour le backend:', media);
    await postStore.createPost(postContent.value, media);

    postContent.value = '';
    selectedFiles.value = [];
    previews.value = [];

  } catch (error) {
    console.error("Erreur publication:", error);
  }
};
</script>

<template>
  <div class="bg-white rounded-3xl border border-gray-200 p-6 shadow-sm mb-6">
    <div class="flex gap-4">
      <img src="https://ui-avatars.com/api/?name=Abderrazak&bg=064E3B&color=fff"
           class="h-12 w-12 rounded-2xl object-cover" alt="Avatar">
      <textarea
        v-model="postContent"
        placeholder="Partagez vos performances ou un nouveau contrat..."
        class="w-full border-none focus:ring-0 resize-none text-md bg-gray-50 rounded-2xl p-4 min-h-[100px] placeholder:text-gray-400 font-medium"
      ></textarea>
    </div>

    <div v-if="previews.length > 0" class="flex gap-3 mt-4 overflow-x-auto pb-2">
      <div v-for="(preview, index) in previews" :key="index" class="relative shrink-0">
        <img v-if="preview.type === 'IMAGE'" :src="preview.url" class="h-24 w-24 rounded-2xl object-cover border shadow-sm" />
        <video v-else :src="preview.url" class="h-24 w-24 rounded-2xl object-cover border shadow-sm"></video>

        <button @click="removeFile(index)" class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full text-xs flex items-center justify-center shadow-lg">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    </div>

    <div v-if="isUploading" class="mt-4">
      <div class="flex justify-between text-[10px] font-black uppercase text-gray-400 mb-1">
        <span>Téléchargement vers Cloudinary...</span>
        <span>{{ uploadProgress }}%</span>
      </div>
      <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
        <div class="bg-emerald-600 h-full transition-all duration-300" :style="{ width: uploadProgress + '%' }"></div>
      </div>
    </div>

    <div class="flex justify-between items-center mt-6 pt-5 border-t border-gray-50">
      <div class="flex gap-2">
        <input type="file" id="photo-up" hidden @change="handleFileSelect" accept="image/*" multiple>
        <input type="file" id="video-up" hidden @change="handleFileSelect" accept="video/*" multiple>

        <label for="photo-up" class="cursor-pointer flex items-center gap-2 text-[11px] font-black text-gray-500 hover:bg-emerald-50 hover:text-emerald-700 px-4 py-2.5 rounded-xl transition uppercase tracking-wider">
          <i class="fa-solid fa-image text-emerald-600 text-sm"></i> Photo
        </label>

        <label for="video-up" class="cursor-pointer flex items-center gap-2 text-[11px] font-black text-gray-500 hover:bg-red-50 hover:text-red-700 px-4 py-2.5 rounded-xl transition uppercase tracking-wider">
          <i class="fa-solid fa-video text-red-500 text-sm"></i> Vidéo
        </label>
      </div>

      <button
        @click="handlePublish"
        :disabled="isUploading || (!postContent && selectedFiles.length === 0)"
        class="bg-[#064E3B] hover:bg-[#043a2c] disabled:bg-gray-200 text-white px-8 py-2.5 rounded-xl font-black text-xs uppercase tracking-widest transition-all shadow-md active:scale-95"
      >
        <span v-if="isUploading">Patientez...</span>
        <span v-else>Publier</span>
      </button>
    </div>
  </div>
</template>
