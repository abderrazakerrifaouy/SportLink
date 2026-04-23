<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100">
      <h2 class="text-lg font-bold text-gray-900">Create Post</h2>
    </div>
    <div class="p-4">
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div class="flex gap-3">
          <img :src="userAvatar" class="w-12 h-12 rounded-full object-cover border-2 border-gray-100">
          <div class="flex-1">
            <textarea
              v-model="content"
              placeholder="What's on your mind?"
              rows="3"
              class="w-full resize-none rounded-xl border-0 bg-gray-50 p-3 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all outline-none"
              :disabled="submitting"
            ></textarea>
          </div>
        </div>

        <div v-if="selectedMedia.length > 0" class="flex gap-2 flex-wrap p-2 bg-gray-50 rounded-xl">
          <div v-for="(item, index) in selectedMedia" :key="index" class="relative group">
            <img v-if="item.type === 'IMAGE'" :src="item.preview" class="w-20 h-20 rounded-xl object-cover shadow-sm">
            <div v-else class="w-20 h-20 rounded-xl bg-black flex items-center justify-center relative">
               <video :src="item.preview" class="w-full h-full rounded-xl object-cover opacity-50"></video>
               <span class="absolute text-white text-xs font-bold">VIDEO</span>
            </div>
            <button @click="removeMedia(index)" type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between pt-2">
          <button type="button" @click="triggerMediaUpload" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
          </button>
          <input ref="mediaInput" type="file" multiple accept="image/*,video/*" class="hidden" @change="handleMediaSelect">

          <button
            type="submit"
            :disabled="!content.trim() || submitting"
            class="px-6 py-2 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 disabled:opacity-50 transition-all shadow-md shadow-blue-200"
          >
            {{ submitting ? 'Uploading...' : 'Post' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePostStore } from '@/stores/PostStore'
import { useAuthStore } from '@/stores/AuthStore'
import { useUploadMedia } from '@/services/useUploadMedia'

const emit = defineEmits(['created'])
const postStore = usePostStore()
const authStore = useAuthStore()
const { uploadMedia } = useUploadMedia()

const content = ref('')
const selectedMedia = ref([])
const submitting = ref(false)
const mediaInput = ref(null)

const userAvatar = computed(() => authStore.user?.profileImage || '/default-avatar.png')

const triggerMediaUpload = () => mediaInput.value?.click()

const handleMediaSelect = (event) => {
  const files = Array.from(event.target.files)
  files.forEach(file => {
    selectedMedia.value.push({
      file,
      preview: URL.createObjectURL(file),
      type: file.type.startsWith('video') ? 'VIDEO' : 'IMAGE'
    })
  })
}

const removeMedia = (index) => selectedMedia.value.splice(index, 1)

const handleSubmit = async () => {
  if (!content.value.trim() || submitting.value) return
  submitting.value = true

  try {
    let mediaData = []
    if (selectedMedia.value.length > 0) {
      const filesToUpload = selectedMedia.value.map(m => m.file)
      const results = await uploadMedia(filesToUpload)
      mediaData = results.map(res => ({
        url: res.url,
        mediaType: res.type // IMAGE ou VIDEO
      }))
    }

    await postStore.createPost(content.value, mediaData)
    content.value = ''
    selectedMedia.value = []
    emit('created')
  } catch (err) {
    console.error("Post creation error:", err)
  } finally {
    submitting.value = false
  }
}
</script>
