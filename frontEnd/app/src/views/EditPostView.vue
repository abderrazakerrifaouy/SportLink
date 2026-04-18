<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePostStore } from '@/stores/PostStore'
import { useUploadMedia } from '@/services/useUploadMedia'
import MainLayout from '@/layouts/MainLayout.vue'

const route = useRoute()
const router = useRouter()
const postStore = usePostStore()
const { uploadMedia, isUploading, uploadProgress } = useUploadMedia()

const postId = computed(() => Number(route.params.id))
const postContent = ref('')
const existingMedia = ref([])
const selectedFiles = ref([])
const previews = ref([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')

const loadPost = async () => {
  loading.value = true
  try {
    const post = await postStore.fetchPostById(postId.value)
    postContent.value = post.content || ''
    existingMedia.value = post.media || []
  } catch {
    error.value = 'Impossible de charger le post.'
  } finally {
    loading.value = false
  }
}

const handleFileSelect = (e) => {
  const files = Array.from(e.target.files)
  files.forEach((file) => {
    selectedFiles.value.push(file)
    const reader = new FileReader()
    reader.onload = (ev) => {
      previews.value.push({
        id: Date.now() + Math.random(),
        url: ev.target.result,
        type: file.type.startsWith('video') ? 'VIDEO' : 'IMAGE',
        file,
        name: file.name,
      })
    }
    reader.readAsDataURL(file)
  })
  e.target.value = ''
}

const removeNewFile = (id) => {
  const idx = previews.value.findIndex((p) => p.id === id)
  if (idx !== -1) {
    selectedFiles.value.splice(idx, 1)
    previews.value.splice(idx, 1)
  }
}

const removeExistingMedia = (index) => {
  existingMedia.value.splice(index, 1)
}

const handleSave = async () => {
  if (!postContent.value.trim() && existingMedia.value.length === 0 && selectedFiles.value.length === 0) {
    error.value = 'Le post ne peut pas être vide.'
    return
  }
  saving.value = true
  error.value = ''
  try {
    let uploadedMedia = []
    if (selectedFiles.value.length > 0) {
      uploadedMedia = await uploadMedia(selectedFiles.value)
    }
    const media = [
      ...existingMedia.value.map((m) => ({ url: m.url, mediaType: m.type })),
      ...uploadedMedia.map((m) => ({ url: m.url, mediaType: m.type.toUpperCase() })),
    ]
    await postStore.updatePost(postId.value, postContent.value.trim(), media)
    router.push(`/posts/${postId.value}`)
  } catch (err) {
    error.value = err.response?.data?.message || 'Erreur lors de la sauvegarde.'
  } finally {
    saving.value = false
  }
}

onMounted(loadPost)
</script>

<template>
  <MainLayout>
    <div class="max-w-2xl mx-auto">
      <!-- Back -->
      <div class="mb-5">
        <button
          @click="router.back()"
          class="flex items-center gap-2 text-sm text-gray-500 hover:text-emerald-700 font-semibold transition"
        >
          <i class="fa-solid fa-arrow-left"></i> Retour
        </button>
      </div>

      <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <h1 class="text-xl font-black text-gray-900 mb-6">
          <i class="fa-regular fa-pen-to-square text-emerald-600 mr-2"></i>Modifier le post
        </h1>

        <!-- Loading -->
        <div v-if="loading" class="animate-pulse space-y-4">
          <div class="h-32 bg-gray-100 rounded-2xl"></div>
          <div class="h-10 bg-gray-100 rounded-xl"></div>
        </div>

        <div v-else class="space-y-5">
          <!-- Error -->
          <div v-if="error" class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            <i class="fa-solid fa-circle-exclamation mr-2"></i>{{ error }}
          </div>

          <!-- Text -->
          <div>
            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 block">Contenu</label>
            <textarea
              v-model="postContent"
              class="w-full border border-gray-200 bg-gray-50 rounded-2xl p-4 min-h-[140px] text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent resize-none outline-none placeholder:text-gray-400"
              placeholder="Partagez vos performances..."
              maxlength="5000"
            ></textarea>
            <div class="text-right text-xs text-gray-400 mt-1">{{ postContent.length }}/5000</div>
          </div>

          <!-- Existing media -->
          <div v-if="existingMedia.length > 0">
            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 block">Médias existants</label>
            <div class="flex gap-3 flex-wrap">
              <div v-for="(item, index) in existingMedia" :key="index" class="relative">
                <img
                  v-if="item.type === 'IMAGE'"
                  :src="item.url"
                  class="h-24 w-24 rounded-2xl object-cover border border-gray-200"
                />
                <div v-else class="h-24 w-24 rounded-2xl bg-gray-900 flex items-center justify-center border border-gray-200">
                  <i class="fa-solid fa-video text-white text-2xl"></i>
                </div>
                <button
                  @click="removeExistingMedia(index)"
                  class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full text-xs flex items-center justify-center"
                >
                  <i class="fa-solid fa-xmark"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- New media previews -->
          <div v-if="previews.length > 0">
            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 block">Nouveaux médias</label>
            <div class="flex gap-3 flex-wrap">
              <div v-for="preview in previews" :key="preview.id" class="relative">
                <img
                  v-if="preview.type === 'IMAGE'"
                  :src="preview.url"
                  class="h-24 w-24 rounded-2xl object-cover border border-gray-200"
                />
                <div v-else class="h-24 w-24 rounded-2xl bg-gray-900 flex items-center justify-center border border-gray-200">
                  <i class="fa-solid fa-video text-white text-2xl"></i>
                </div>
                <button
                  @click="removeNewFile(preview.id)"
                  class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full text-xs flex items-center justify-center"
                >
                  <i class="fa-solid fa-xmark"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Upload progress -->
          <div v-if="isUploading">
            <div class="flex justify-between text-xs font-bold text-gray-400 mb-1">
              <span>Upload en cours...</span><span>{{ uploadProgress }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5">
              <div class="bg-emerald-600 h-1.5 rounded-full transition-all" :style="{ width: uploadProgress + '%' }"></div>
            </div>
          </div>

          <!-- Add media -->
          <div class="flex gap-3">
            <input type="file" id="edit-photo" hidden @change="handleFileSelect" accept="image/*" multiple />
            <input type="file" id="edit-video" hidden @change="handleFileSelect" accept="video/*" multiple />
            <label for="edit-photo" class="cursor-pointer flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-gray-500 hover:bg-emerald-50 hover:text-emerald-700 border border-gray-200 transition">
              <i class="fa-solid fa-image text-emerald-600"></i> Photo
            </label>
            <label for="edit-video" class="cursor-pointer flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-gray-500 hover:bg-red-50 hover:text-red-700 border border-gray-200 transition">
              <i class="fa-solid fa-video text-red-500"></i> Vidéo
            </label>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-2 border-t border-gray-100">
            <button
              @click="router.back()"
              class="flex-1 py-3 rounded-xl text-sm font-bold border border-gray-200 text-gray-700 hover:bg-gray-50 transition"
            >
              Annuler
            </button>
            <button
              @click="handleSave"
              :disabled="saving || isUploading"
              class="flex-1 py-3 rounded-xl text-sm font-bold bg-emerald-700 text-white hover:bg-emerald-800 transition disabled:opacity-50"
            >
              <i v-if="saving || isUploading" class="fa-solid fa-spinner animate-spin mr-2"></i>
              {{ saving || isUploading ? 'Sauvegarde...' : 'Sauvegarder' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
