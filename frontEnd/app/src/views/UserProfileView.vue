<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/AuthStore'
import { useUserStore } from '@/stores/userStore'
import { usePostStore } from '@/stores/PostStore'
import { storeToRefs } from 'pinia'
import MainLayout from '@/layouts/MainLayout.vue'
import PostComponent from '@/components/PostComponent.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const userStore = useUserStore()
const postStore = usePostStore()
const { user: currentUser } = storeToRefs(authStore)

const profile = ref(null)
const userPosts = ref([])
const followers = ref([])
const following = ref([])
const isFollowing = ref(false)
const loading = ref(true)
const postsLoading = ref(false)
const followLoading = ref(false)
const activeTab = ref('posts')

const userId = computed(() => Number(route.params.id))
const isOwnProfile = computed(() => currentUser.value?.id === userId.value)

const avatarUrl = (name, img) =>
  img || `https://ui-avatars.com/api/?name=${encodeURIComponent(name || 'U')}&bg=064E3B&color=fff&size=200`

const loadProfile = async () => {
  loading.value = true
  try {
    profile.value = await userStore.fetchUser(userId.value)
    // Check following status
    if (!isOwnProfile.value && currentUser.value) {
      const res = await userStore.isFollowing(userId.value)
      isFollowing.value = res?.is_following ?? false
    }
    // Load follower/following counts
    followers.value = await userStore.fetchFollowers(userId.value)
    following.value = await userStore.fetchFollowing(userId.value)
  } catch (err) {
    console.error('Failed to load profile:', err)
  } finally {
    loading.value = false
  }
}

const loadPosts = async () => {
  postsLoading.value = true
  try {
    userPosts.value = await postStore.fetchPostsByUser(userId.value)
  } catch (err) {
    console.error('Failed to load posts:', err)
  } finally {
    postsLoading.value = false
  }
}

const toggleFollow = async () => {
  followLoading.value = true
  try {
    if (isFollowing.value) {
      await userStore.unfollow(userId.value)
      isFollowing.value = false
      followers.value = followers.value.filter((f) => f.id !== currentUser.value?.id)
    } else {
      await userStore.follow(userId.value)
      isFollowing.value = true
      followers.value.push(currentUser.value)
    }
  } catch (err) {
    console.error('Follow error:', err)
  } finally {
    followLoading.value = false
  }
}

const goToMessages = () => {
  router.push(`/messages/${userId.value}`)
}

watch(userId, () => {
  loadProfile()
  loadPosts()
})

onMounted(() => {
  loadProfile()
  loadPosts()
})
</script>

<template>
  <MainLayout>
    <!-- Loading -->
    <div v-if="loading" class="animate-pulse space-y-4">
      <div class="h-48 bg-gray-200 rounded-3xl"></div>
      <div class="h-32 bg-white rounded-3xl border border-gray-100"></div>
    </div>

    <div v-else-if="profile" class="space-y-6">
      <!-- Banner + Avatar -->
      <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm">
        <!-- Banner -->
        <div class="h-48 bg-gradient-to-br from-emerald-800 to-emerald-600 relative">
          <img
            v-if="profile.bannerImage"
            :src="profile.bannerImage"
            class="w-full h-full object-cover"
            alt="Banner"
          />
          <div class="absolute inset-0 bg-black/20"></div>
        </div>

        <!-- Profile info -->
        <div class="px-6 pb-6">
          <div class="flex items-end justify-between -mt-16 mb-4">
            <img
              :src="avatarUrl(profile.name, profile.profileImage)"
              class="h-28 w-28 rounded-3xl object-cover border-4 border-white shadow-xl"
              :alt="profile.name"
            />
            <div class="flex gap-3 mb-2">
              <template v-if="!isOwnProfile">
                <button
                  @click="toggleFollow"
                  :disabled="followLoading"
                  :class="[
                    'px-6 py-2.5 rounded-xl font-bold text-sm transition-all disabled:opacity-50',
                    isFollowing
                      ? 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                      : 'bg-emerald-700 text-white hover:bg-emerald-800',
                  ]"
                >
                  <i v-if="followLoading" class="fa-solid fa-spinner animate-spin mr-2"></i>
                  {{ isFollowing ? 'Ne plus suivre' : 'Suivre' }}
                </button>
                <button
                  @click="goToMessages"
                  class="px-6 py-2.5 rounded-xl font-bold text-sm border border-gray-200 text-gray-700 hover:bg-gray-50 transition"
                >
                  <i class="fa-regular fa-envelope mr-2"></i>Message
                </button>
              </template>
              <template v-else>
                <RouterLink
                  to="/settings"
                  class="px-6 py-2.5 rounded-xl font-bold text-sm border border-gray-200 text-gray-700 hover:bg-gray-50 transition no-underline"
                >
                  <i class="fa-regular fa-pen-to-square mr-2"></i>Modifier le profil
                </RouterLink>
              </template>
            </div>
          </div>

          <h1 class="text-2xl font-black text-gray-900">{{ profile.name }}</h1>
          <p class="text-sm font-bold text-emerald-700 uppercase tracking-widest mt-0.5">{{ profile.role }}</p>
          <p v-if="profile.bio" class="text-sm text-gray-600 mt-3 max-w-xl leading-relaxed">{{ profile.bio }}</p>

          <!-- Stats -->
          <div class="flex gap-8 mt-5">
            <button @click="activeTab = 'followers'" class="text-center group">
              <div class="text-2xl font-black text-gray-900 group-hover:text-emerald-700 transition">{{ followers.length }}</div>
              <div class="text-xs text-gray-500 font-medium uppercase tracking-widest">Abonnés</div>
            </button>
            <button @click="activeTab = 'following'" class="text-center group">
              <div class="text-2xl font-black text-gray-900 group-hover:text-emerald-700 transition">{{ following.length }}</div>
              <div class="text-xs text-gray-500 font-medium uppercase tracking-widest">Abonnements</div>
            </button>
            <div class="text-center">
              <div class="text-2xl font-black text-gray-900">{{ userPosts.length }}</div>
              <div class="text-xs text-gray-500 font-medium uppercase tracking-widest">Posts</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="flex gap-1 bg-white rounded-2xl border border-gray-100 p-1.5 shadow-sm">
        <button
          v-for="tab in ['posts', 'followers', 'following']"
          :key="tab"
          @click="activeTab = tab"
          :class="[
            'flex-1 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest transition',
            activeTab === tab ? 'bg-emerald-700 text-white' : 'text-gray-500 hover:text-gray-700',
          ]"
        >
          {{ tab === 'posts' ? 'Publications' : tab === 'followers' ? 'Abonnés' : 'Abonnements' }}
        </button>
      </div>

      <!-- Posts tab -->
      <div v-if="activeTab === 'posts'">
        <div v-if="postsLoading" class="space-y-4">
          <div v-for="n in 3" :key="n" class="bg-white rounded-3xl border border-gray-100 p-5 shadow-sm animate-pulse h-48"></div>
        </div>
        <div v-else-if="userPosts.length === 0" class="bg-white rounded-3xl border border-gray-100 p-10 text-center shadow-sm">
          <i class="fa-regular fa-newspaper text-gray-300 text-4xl mb-3"></i>
          <p class="text-gray-500 font-medium">Aucune publication pour le moment</p>
        </div>
        <div v-else class="space-y-5">
          <PostComponent v-for="post in userPosts" :key="post.id" :post="post" />
        </div>
      </div>

      <!-- Followers tab -->
      <div v-else-if="activeTab === 'followers'" class="bg-white rounded-3xl border border-gray-100 p-5 shadow-sm">
        <h3 class="font-bold text-gray-800 mb-4">{{ followers.length }} abonné{{ followers.length !== 1 ? 's' : '' }}</h3>
        <div v-if="followers.length === 0" class="text-center py-8">
          <i class="fa-regular fa-user text-gray-300 text-4xl mb-3"></i>
          <p class="text-gray-500 text-sm">Aucun abonné</p>
        </div>
        <div v-else class="space-y-3">
          <div
            v-for="follower in followers"
            :key="follower.id"
            class="flex items-center justify-between p-3 rounded-2xl hover:bg-gray-50 transition cursor-pointer"
            @click="router.push(`/users/${follower.id}`)"
          >
            <div class="flex items-center gap-3">
              <img
                :src="avatarUrl(follower.name, follower.profileImage)"
                class="h-12 w-12 rounded-2xl object-cover"
                :alt="follower.name"
              />
              <div>
                <p class="font-bold text-gray-800">{{ follower.name }}</p>
                <p class="text-xs text-emerald-600 font-medium uppercase">{{ follower.role }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Following tab -->
      <div v-else-if="activeTab === 'following'" class="bg-white rounded-3xl border border-gray-100 p-5 shadow-sm">
        <h3 class="font-bold text-gray-800 mb-4">{{ following.length }} abonnement{{ following.length !== 1 ? 's' : '' }}</h3>
        <div v-if="following.length === 0" class="text-center py-8">
          <i class="fa-regular fa-user text-gray-300 text-4xl mb-3"></i>
          <p class="text-gray-500 text-sm">Ne suit personne</p>
        </div>
        <div v-else class="space-y-3">
          <div
            v-for="followed in following"
            :key="followed.id"
            class="flex items-center justify-between p-3 rounded-2xl hover:bg-gray-50 transition cursor-pointer"
            @click="router.push(`/users/${followed.id}`)"
          >
            <div class="flex items-center gap-3">
              <img
                :src="avatarUrl(followed.name, followed.profileImage)"
                class="h-12 w-12 rounded-2xl object-cover"
                :alt="followed.name"
              />
              <div>
                <p class="font-bold text-gray-800">{{ followed.name }}</p>
                <p class="text-xs text-emerald-600 font-medium uppercase">{{ followed.role }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Not found -->
    <div v-else class="bg-white rounded-3xl border border-gray-100 p-10 text-center shadow-sm">
      <i class="fa-solid fa-user-slash text-gray-300 text-5xl mb-4"></i>
      <h3 class="font-bold text-gray-800 mb-2">Utilisateur introuvable</h3>
      <RouterLink to="/" class="text-emerald-700 font-semibold text-sm hover:underline">Retour à l'accueil</RouterLink>
    </div>
  </MainLayout>
</template>
