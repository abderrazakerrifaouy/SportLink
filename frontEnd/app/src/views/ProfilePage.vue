<template>
  <div class="max-w-4xl mx-auto space-y-6 pb-12">
    <div v-if="loading" class="bg-white rounded-3xl p-8 animate-pulse shadow-sm">
      <div class="h-32 bg-gray-100 rounded-t-3xl -m-8 mb-8"></div>
      <div class="flex items-end gap-4 -mt-16 mb-8 px-4">
        <div class="w-28 h-28 bg-gray-200 rounded-full border-4 border-white"></div>
        <div class="flex-1 space-y-3 pb-2">
          <div class="h-6 bg-gray-200 rounded w-48"></div>
          <div class="h-4 bg-gray-200 rounded w-32"></div>
        </div>
      </div>
      <div class="space-y-4">
        <div class="h-20 bg-gray-50 rounded-2xl"></div>
        <div class="h-40 bg-gray-50 rounded-2xl"></div>
      </div>
    </div>

    <div v-else-if="user" class="space-y-6">
      <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="h-40 bg-linear-to-r from-indigo-600 via-blue-500 to-teal-400 relative">
          <img v-if="user.bannerImage" :src="user.bannerImage" class="w-full h-full object-cover opacity-40">
        </div>

        <div class="px-6 pb-8">
          <div class="relative flex flex-col md:flex-row md:items-end justify-between -mt-14 gap-4">
            <div class="flex flex-col md:flex-row items-center md:items-end gap-4 text-center md:text-left">
              <div class="relative group">
                <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white">
                  <img
                    :src="editMode ? (editData.profileImage || '/default-avatar.png') : (user.profileImage || '/default-avatar.png')"
                    class="w-full h-full object-cover"
                  >
                </div>
                <button
                  v-if="editMode"
                  @click="triggerImageUpload"
                  class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </button>
                <input ref="imageInput" type="file" accept="image/*" class="hidden" @change="handleImageChange">
              </div>

              <div class="mb-2">
                <template v-if="editMode">
                  <input v-model="editData.name" type="text" class="text-2xl font-bold text-gray-900 bg-gray-50 border border-indigo-200 rounded-xl px-4 py-1 focus:ring-2 focus:ring-indigo-500 outline-none">
                </template>
                <template v-else>
                  <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ user.name }}</h1>
                </template>
                <div class="flex items-center justify-center md:justify-start gap-2 mt-1">
                  <span class="px-3 py-0.5 bg-indigo-50 text-indigo-600 text-xs font-bold uppercase rounded-full tracking-wider">{{ user.role }}</span>
                  <span v-if="user.isActive" class="w-2 h-2 bg-green-500 rounded-full" title="Active"></span>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-center gap-3 pb-2">
              <template v-if="isOwnProfile">
                <button v-if="!editMode" @click="startEdit" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 shadow-md shadow-indigo-200 transition-all active:scale-95">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  Edit
                </button>
                <div v-else class="flex gap-2">
                  <button @click="cancelEdit" class="px-5 py-2.5 bg-gray-100 text-gray-600 rounded-2xl font-bold hover:bg-gray-200 transition">Cancel</button>
                  <button @click="saveProfile" :disabled="saving" class="px-5 py-2.5 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 disabled:opacity-50 shadow-md shadow-indigo-200 transition-all">
                    {{ saving ? 'Saving...' : 'Save Changes' }}
                  </button>
                </div>
              </template>

              <template v-else>
                <button
                  @click="toggleFollow"
                  class="px-8 py-2.5 rounded-2xl font-bold transition-all active:scale-95 shadow-sm"
                  :class="isFollowing ? 'bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200' : 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-indigo-100'"
                >
                  {{ isFollowing ? 'Unfollow' : 'Follow' }}
                </button>
                <button @click="openMessageModal" class="p-2.5 bg-white text-gray-600 border border-gray-200 rounded-2xl hover:bg-gray-50 transition shadow-sm">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </button>
              </template>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-4 mt-8 py-4 border-t border-gray-50">
            <div class="text-center group cursor-pointer">
              <p class="text-xl font-black text-gray-900 group-hover:text-indigo-600 transition-colors">{{ user.stats?.followers_count || 0 }}</p>
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Followers</p>
            </div>
            <div class="text-center group cursor-pointer border-x border-gray-100">
              <p class="text-xl font-black text-gray-900 group-hover:text-indigo-600 transition-colors">{{ user.stats?.following_count || 0 }}</p>
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Following</p>
            </div>
            <div class="text-center group cursor-pointer">
              <p class="text-xl font-black text-gray-900 group-hover:text-indigo-600 transition-colors">{{ user.stats?.posts_count || 0 }}</p>
              <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Posts</p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1 space-y-6">
          <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">About Me</h2>
            <template v-if="editMode">
              <textarea v-model="editData.bio" rows="5" class="w-full bg-gray-50 border border-indigo-100 rounded-2xl px-4 py-3 text-gray-700 focus:ring-2 focus:ring-indigo-500 outline-none text-sm leading-relaxed" placeholder="Tell us about your journey..."></textarea>
            </template>
            <template v-else>
              <p v-if="user.bio" class="text-gray-600 text-sm leading-relaxed">{{ user.bio }}</p>
              <p v-else class="text-gray-400 text-sm italic">This athlete hasn't shared a bio yet.</p>
            </template>
          </div>

          <div v-if="!isOwnProfile" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Safety</h2>
            <p class="text-sm text-gray-600 leading-relaxed">
              If this profile breaks the community rules, send a report to the moderation team for review.
            </p>
            <button
              @click="openReportModal"
              class="mt-4 w-full px-4 py-3 bg-rose-600 text-white rounded-2xl font-bold hover:bg-rose-700 transition-all active:scale-[0.99]"
            >
              Report user
            </button>
            <p v-if="reportNotice" class="mt-3 text-sm font-medium text-emerald-600">
              {{ reportNotice }}
            </p>
          </div>
        </div>

        <div class="md:col-span-2 space-y-6">
          <div class="flex items-center justify-between px-2">
            <h2 class="text-xl font-black text-gray-900 tracking-tight">Recent Posts</h2>
            <div class="h-1 flex-1 mx-4 bg-gray-100 rounded-full"></div>
          </div>

          <div v-if="userPosts.length > 0" class="space-y-6">
            <PostCard
              v-for="post in userPosts"
              :key="post.id"
              :post="post"
              @post-mutated="fetchUserPosts"
              @deleted="fetchUserPosts"
            />
          </div>

          <div v-else class="bg-gray-50 rounded-3xl p-12 text-center border-2 border-dashed border-gray-200">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
              <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="text-gray-500 font-medium">No posts to show right now</p>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="bg-white rounded-3xl p-16 text-center shadow-sm border border-gray-100">
      <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
      </div>
      <h3 class="text-xl font-bold text-gray-900">User not found</h3>
      <p class="text-gray-500 mt-2">The profile you are looking for doesn't exist or has been moved.</p>
      <button @click="router.back()" class="mt-6 text-indigo-600 font-bold hover:underline">Go Back</button>
    </div>

    <div v-if="showMessageModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="closeMessageModal">
      <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl scale-in">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-black text-gray-900">Message to {{ user?.name }}</h3>
          <button @click="closeMessageModal" class="p-2 hover:bg-gray-100 rounded-full transition">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
        <textarea
          v-model="messageContent"
          rows="5"
          class="w-full bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3 text-gray-900 focus:ring-2 focus:ring-indigo-500 outline-none resize-none transition-all"
          placeholder="Type your message here..."
        ></textarea>
        <div class="flex gap-3 mt-6">
          <button @click="closeMessageModal" class="flex-1 px-4 py-3 bg-gray-100 text-gray-600 rounded-2xl font-bold hover:bg-gray-200 transition">Cancel</button>
          <button
            @click="sendMessage"
            :disabled="!messageContent.trim() || sendingMessage"
            class="flex-1 px-4 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 transition disabled:opacity-50 shadow-lg shadow-indigo-200"
          >
            {{ sendingMessage ? 'Sending...' : 'Send Message' }}
          </button>
        </div>
      </div>
    </div>

    <ReportModal
      :is-open="showReportModal"
      reportable-type="App\Models\User"
      :reportable-id="user?.id || 0"
      @close="closeReportModal"
      @success="handleReportSuccess"
    />
  </div>
</template>

<style scoped>
.scale-in {
  animation: scaleIn 0.2s ease-out;
}
@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
</style>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/AuthStore'
import { useUserStore } from '@/stores/userStore'
import { usePostStore } from '@/stores/PostStore'
import { useUploadMedia } from '@/services/useUploadMedia'
import PostCard from '@/components/posts/PostCard.vue'
import ReportModal from '@/components/ReportModal.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const userStore = useUserStore()
const postStore = usePostStore()
const { uploadMedia } = useUploadMedia()

const loading = ref(true)
const saving = ref(false)
const editMode = ref(false)
const editData = ref({ name: '', bio: '', profileImage: null })
const imageInput = ref(null)

const showMessageModal = ref(false)
const messageContent = ref('')
const sendingMessage = ref(false)
const userPosts = ref([])
const showReportModal = ref(false)
const reportNotice = ref('')

const userId = computed(() => route.params.id ? parseInt(route.params.id) : authStore.user?.id)
const isOwnProfile = computed(() => !route.params.id || route.params.id == authStore.user?.id)
const user = computed(() => isOwnProfile.value ? authStore.user : userStore.currentProfile)
const isFollowing = ref(false)

const fetchUser = async () => {
  loading.value = true
  try {
    if (isOwnProfile.value) {
      await authStore.fetchUser()
    } else if (userId.value) {
      await userStore.fetchUser(userId.value)
      // تصحيح: حيدنا "!" اللي كانت قالبا المنطق
      isFollowing.value = await userStore.isFollowing(userId.value)
      isFollowing.value = isFollowing.value.is_following
      console.log('Is following:', isFollowing.value)
    }
  } catch (error) {
    console.error('Failed to fetch user:', error)
  } finally {
    loading.value = false
  }
}

const fetchUserPosts = async () => {
  if (!userId.value) return
  try {
    const posts = await postStore.fetchPostsByUser(userId.value)
    userPosts.value = posts || []
  } catch (error) {
    console.error('Failed to fetch posts:', error)
  }
}

const startEdit = () => {
  editData.value = {
    name: user.value?.name || '',
    bio: user.value?.bio || '',
    profileImage: user.value?.profileImage || null
  }
  editMode.value = true
}

const cancelEdit = () => {
  editMode.value = false
  editData.value = { name: '', bio: '', profileImage: null }
}

const triggerImageUpload = () => {
  imageInput.value?.click()
}

const handleImageChange = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  try {
    const uploaded = await uploadMedia([file])
    if (uploaded && uploaded[0]) {
      editData.value.profileImage = uploaded[0].url
    }
  } catch (error) {
    console.error('Failed to upload image:', error)
  }
}

const saveProfile = async () => {
  saving.value = true
  try {
    await userStore.updateUser(user.value.id, {
      name: editData.value.name,
      bio: editData.value.bio,
      profileImage: editData.value.profileImage
    })
    await authStore.fetchUser()
    editMode.value = false
  } catch (error) {
    console.error('Failed to save profile:', error)
  } finally {
    saving.value = false
  }
}

const toggleFollow = async () => {
  if (!userId.value) return

  // حفظ الحالة القديمة للرجوع إليها في حالة الخطأ (Optimistic Update)
  const prevState = isFollowing.value

  try {
    if (isFollowing.value) {
      isFollowing.value = false
      await userStore.unfollow(userId.value)
    } else {
      isFollowing.value = true
      await userStore.follow(userId.value)
    }
    // تحديث بيانات البروفايل باش الـ followers_count يتبدل فالبلاصة
    await userStore.fetchUser(userId.value)
  } catch (error) {
    isFollowing.value = prevState
    console.error('Failed to toggle follow:', error)
  }
}

const openMessageModal = () => {
  showMessageModal.value = true
}

const closeMessageModal = () => {
  showMessageModal.value = false
  messageContent.value = ''
}

const sendMessage = async () => {
  if (!messageContent.value.trim() || !userId.value) return
  sendingMessage.value = true
  try {
    await userStore.sendMessage(userId.value, messageContent.value)
    closeMessageModal()
    router.push('/dashboard/messages')
  } catch (error) {
    console.error('Failed to send message:', error)
  } finally {
    sendingMessage.value = false
  }
}

const openReportModal = () => {
  reportNotice.value = ''
  showReportModal.value = true
}

const closeReportModal = () => {
  showReportModal.value = false
}

const handleReportSuccess = () => {
  reportNotice.value = 'Report submitted successfully. Moderators will review it shortly.'
}

onMounted(async () => {
  await fetchUser()
  await fetchUserPosts()
})

watch(() => route.params.id, async () => {
  await fetchUser()
  await fetchUserPosts()
})
</script>
