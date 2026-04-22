<template>
  <div class="space-y-6">
    <div v-if="loading" class="bg-white rounded-2xl p-8 animate-pulse">
      <div class="flex items-center gap-4 mb-8">
        <div class="w-24 h-24 bg-gray-200 rounded-full"></div>
        <div class="space-y-2">
          <div class="h-6 bg-gray-200 rounded w-32"></div>
          <div class="h-4 bg-gray-200 rounded w-24"></div>
        </div>
      </div>
    </div>

    <div v-else-if="user" class="space-y-6">
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-blue-500 to-indigo-600"></div>
        <div class="px-6 pb-6">
          <div class="relative -mt-12 mb-4">
            <div class="relative inline-block">
              <img
                :src="editMode ? editData.profileImage || '/default-avatar.png' : (user.profileImage || '/default-avatar.png')"
                class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md"
              >
              <button
                v-if="editMode"
                @click="triggerImageUpload"
                class="absolute bottom-0 right-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </button>
              <input ref="imageInput" type="file" accept="image/*" class="hidden" @change="handleImageChange">
            </div>
          </div>

          <div class="flex items-start justify-between">
            <div class="flex-1">
              <template v-if="editMode">
                <input v-model="editData.name" type="text" class="text-2xl font-bold text-gray-900 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1 w-full mb-1">
              </template>
              <template v-else>
                <h1 class="text-2xl font-bold text-gray-900">{{ user.name }}</h1>
              </template>
              <p class="text-gray-500 capitalize">{{ user.role }}</p>
            </div>

            <div class="flex gap-2">
              <template v-if="isOwnProfile">
                <button v-if="!editMode" @click="startEdit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-medium hover:bg-gray-200 transition">
                  Edit Profile
                </button>
                <template v-else>
                  <button @click="cancelEdit" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-medium hover:bg-gray-200 transition">
                    Cancel
                  </button>
                  <button @click="saveProfile" :disabled="saving" class="px-4 py-2 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition disabled:opacity-50">
                    {{ saving ? 'Saving...' : 'Save' }}
                  </button>
                </template>
              </template>

              <template v-else>
                <button
                  @click="toggleFollow"
                  class="px-4 py-2 rounded-xl font-medium transition min-w-[100px]"
                  :class="isFollowing ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-blue-600 text-white hover:bg-blue-700'"
                >
                  {{ isFollowing ? 'Unfollow' : 'Follow' }}
                </button>
                <button @click="openMessageModal" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-medium hover:bg-gray-200 transition">
                  Message
                </button>
              </template>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">About</h2>
        <template v-if="editMode">
          <textarea v-model="editData.bio" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-gray-600" placeholder="Write something about yourself..."></textarea>
        </template>
        <template v-else>
          <p v-if="user.bio" class="text-gray-600">{{ user.bio }}</p>
          <p v-else class="text-gray-400 italic">No bio yet</p>
        </template>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Stats</h2>
        <div class="grid grid-cols-3 gap-4">
          <div class="text-center">
            <p class="text-2xl font-bold text-gray-900">{{ user.stats?.followers_count || 0 }}</p>
            <p class="text-sm text-gray-500">Followers</p>
          </div>
          <div class="text-center">
            <p class="text-2xl font-bold text-gray-900">{{ user.stats?.following_count || 0 }}</p>
            <p class="text-sm text-gray-500">Following</p>
          </div>
          <div class="text-center">
            <p class="text-2xl font-bold text-gray-900">{{ user.stats?.posts_count || 0 }}</p>
            <p class="text-sm text-gray-500">Posts</p>
          </div>
        </div>
      </div>

      <div v-if="userPosts.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Posts</h2>
        <div class="space-y-4">
          <PostCard v-for="post in userPosts" :key="post.id" :post="post" />
        </div>
      </div>
    </div>

    <div v-else class="bg-white rounded-2xl p-8 text-center">
      <p class="text-gray-500">User not found</p>
    </div>

    <div v-if="showMessageModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="closeMessageModal">
      <div class="bg-white rounded-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-bold text-gray-900">Send Message</h3>
          <button @click="closeMessageModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <textarea v-model="messageContent" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-gray-900 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none resize-none" placeholder="Write your message..."></textarea>
        <div class="flex justify-end gap-2 mt-4">
          <button @click="closeMessageModal" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-medium hover:bg-gray-200 transition">
            Cancel
          </button>
          <button @click="sendMessage" :disabled="!messageContent.trim() || sendingMessage" class="px-4 py-2 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition disabled:opacity-50">
            {{ sendingMessage ? 'Sending...' : 'Send' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/AuthStore'
import { useUserStore } from '@/stores/userStore'
import { usePostStore } from '@/stores/PostStore'
import { useUploadMedia } from '@/services/useUploadMedia'
import PostCard from '@/components/posts/PostCard.vue'

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

onMounted(async () => {
  await fetchUser()
  await fetchUserPosts()
})

watch(() => route.params.id, async () => {
  await fetchUser()
  await fetchUserPosts()
})
</script>
