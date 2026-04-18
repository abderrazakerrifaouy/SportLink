<template>
  <MainLayout>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

      <div class="lg:col-span-8 space-y-6">
        <CreatePost />

        <div class="flex flex-col items-center">
          <!-- Loading state -->
          <div v-if="loading" class="text-center py-8">
            <p class="text-gray-500">Chargement des posts...</p>
          </div>

          <!-- Empty state -->
          <div v-else-if="posts.length === 0" class="text-center py-8">
            <p class="text-gray-500">Aucun post pour le moment</p>
          </div>

          <!-- Posts list -->
          <div v-for="post in posts" :key="post.id" class="w-full">
            <PostComponent :post="post" />
          </div>
        </div>
      </div>

      <aside class="lg:col-span-4 hidden lg:flex flex-col gap-6 sticky top-24 h-fit">
        <div class="bg-white rounded-2xl border border-gray-200/60 p-5 shadow-sm">
          <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-4">Suggestions pour vous</h3>
          <div class="space-y-4">
            <SuggestionItem name="Sarah L." job="Coach Sportif" img="https://i.pravatar.cc/150?u=sarah" />
            <SuggestionItem name="David Kim" job="Agent de joueurs" img="https://i.pravatar.cc/150?u=david" />
          </div>
        </div>

        <!-- Ad / Promo -->
        <div class="bg-gradient-to-br from-[#064E3B] to-[#0f766e] rounded-2xl p-6 text-white relative overflow-hidden group shadow-md shadow-emerald-900/10">
          <div class="relative z-10">
            <div class="bg-white/20 w-fit p-2 rounded-lg mb-3">
              <i class="fa-solid fa-rocket text-white"></i>
            </div>
            <h3 class="text-lg font-bold leading-tight mb-2">Boostez votre carrière</h3>
            <p class="text-xs text-emerald-100 font-medium mb-5 leading-relaxed">Passez au compte Pro pour accéder aux offres exclusives des clubs de Ligue 1.</p>
            <button class="bg-white text-[#064E3B] w-full py-2.5 rounded-xl font-bold text-xs hover:bg-gray-50 transition shadow-sm">
              Découvrir SportLink Pro
            </button>
          </div>
          <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/5 rounded-full blur-2xl group-hover:scale-110 transition-transform"></div>
        </div>
      </aside>

    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/layouts/MainLayout.vue'
import SuggestionItem from '@/components/SuggestionItem.vue'
import PostComponent from '@/components/PostComponent.vue'
import CreatePost from '@/components/CreatePost.vue'
import { ref, onMounted, computed } from 'vue'
import { usePostStore } from '@/stores/PostStore'

const postStore = usePostStore()
const loading = ref(false)

const posts = computed(() => postStore.posts)

onMounted(async () => {
  loading.value = true
  try {
    await postStore.fetchPosts()
  } catch (error) {
    console.error('Erreur lors du chargement des posts:', error)
  } finally {
    loading.value = false
  }
})
</script>
