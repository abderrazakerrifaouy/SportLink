<template>
  <MainLayout>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 animate-fade-in">
      <!-- Feed (Posts) -->
      <div class="lg:col-span-8 space-y-5">
        <!-- Create Post Section -->
        <div class="transform transition-all duration-300 hover:translate-y-[-1px]">
          <CreatePost />
        </div>

        <!-- Feed Header -->
        <div class="flex items-center justify-between px-1">
          <h2 class="text-sm font-bold text-gray-500 uppercase tracking-widest">
            <i class="fa-solid fa-newspaper text-emerald-600 mr-2"></i>Fil d'actualité
          </h2>
          <span v-if="!loading" class="text-xs text-gray-400 font-medium bg-white border border-gray-100 px-3 py-1 rounded-full shadow-sm">
            {{ posts.length }} publication{{ posts.length !== 1 ? 's' : '' }}
          </span>
        </div>

        <!-- Loading Skeleton -->
        <div v-if="loading" class="space-y-5">
          <div v-for="n in 3" :key="n" class="bg-white rounded-3xl border border-gray-100 p-5 shadow-sm animate-pulse">
            <div class="flex items-start gap-3 mb-5">
              <div class="h-12 w-12 bg-gray-200 rounded-2xl shrink-0"></div>
              <div class="flex-1 space-y-2 pt-1">
                <div class="h-4 bg-gray-200 rounded-lg w-1/3"></div>
                <div class="h-3 bg-gray-100 rounded-lg w-1/4"></div>
              </div>
            </div>
            <div class="space-y-2 mb-5">
              <div class="h-3 bg-gray-100 rounded-lg w-full"></div>
              <div class="h-3 bg-gray-100 rounded-lg w-4/5"></div>
            </div>
            <div class="aspect-video bg-gray-100 rounded-2xl"></div>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="bg-white rounded-3xl border border-red-100 p-8 shadow-sm text-center">
          <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-triangle-exclamation text-red-500 text-xl"></i>

          </div>
          <h3 class="font-bold text-gray-800 mb-2">Une erreur s'est produite</h3>
          <p class="text-sm text-gray-500 mb-5">{{ error }}</p>
          <button
            @click="reloadPosts"
            class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all active:scale-95 shadow-md shadow-emerald-900/20"
          >
            <i class="fa-solid fa-rotate-right mr-2"></i>Réessayer
          </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="posts.length === 0" class="bg-white rounded-3xl border border-gray-100 p-10 shadow-sm text-center">
          <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-futbol text-emerald-600 text-2xl"></i>
          </div>
          <h3 class="font-bold text-gray-800 mb-2">Aucune publication pour le moment</h3>
          <p class="text-sm text-gray-500">Soyez le premier à partager quelque chose !</p>
        </div>

        <!-- Posts List -->
        <TransitionGroup
          v-else
          name="post-list"
          tag="div"
          class="space-y-5"
        >
          <div
            v-for="post in posts"
            :key="post.id"
            class="transform transition-all duration-300 hover:translate-y-[-2px]"
          >
            <PostComponent :post="post" />
          </div>
        </TransitionGroup>
      </div>
      <!-- Right Sidebar -->
      <aside class="lg:col-span-4 hidden lg:flex flex-col gap-5 sticky top-6 h-fit">

        <!-- Suggestions Card -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
          <div class="px-5 pt-5 pb-3 flex items-center justify-between">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">
              <i class="fa-solid fa-user-plus text-emerald-600 mr-1.5"></i>Suggestions
            </h3>
            <button class="text-[10px] font-bold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider transition">
              Voir tout
            </button>
          </div>
          <div class="px-3 pb-4 space-y-1">
            <SuggestionItem name="Sarah L." job="Coach Sportif" img="https://i.pravatar.cc/150?u=sarah" />
            <SuggestionItem name="David Kim" job="Agent de joueurs" img="https://i.pravatar.cc/150?u=david" />
            <SuggestionItem name="Karim B." job="Joueur Pro · Ligue 2" img="https://i.pravatar.cc/150?u=karim" />
          </div>
        </div>

        <!-- Trending Topics -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
          <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">
            <i class="fa-solid fa-fire text-orange-500 mr-1.5"></i>Tendances Sport
          </h3>
          <div class="space-y-3">
            <div
              v-for="(topic, i) in trendingTopics"
              :key="i"
              class="flex items-center justify-between group cursor-pointer p-2 rounded-xl hover:bg-gray-50 transition-colors"
            >
              <div>
                <p class="text-xs text-gray-400 font-medium">{{ topic.category }}</p>
                <p class="text-sm font-bold text-gray-800 group-hover:text-emerald-700 transition-colors">{{ topic.tag }}</p>
                <p class="text-[10px] text-gray-400">{{ topic.count }} publications</p>
              </div>
              <div class="w-8 h-8 bg-gray-50 group-hover:bg-emerald-50 rounded-xl flex items-center justify-center transition-colors">
                <i class="fa-solid fa-arrow-trend-up text-gray-400 group-hover:text-emerald-600 text-xs transition-colors"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Ad / Promo Card -->
        <div class="bg-gradient-to-br from-[#064E3B] via-[#065f46] to-[#0f766e] rounded-2xl p-6 text-white relative overflow-hidden group shadow-lg shadow-emerald-900/20">
          <!-- Background decorations -->
          <div class="absolute -right-8 -top-8 w-36 h-36 bg-white/5 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-700"></div>
          <div class="absolute -left-4 -bottom-8 w-28 h-28 bg-emerald-300/10 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-700"></div>

          <div class="relative z-10">
            <div class="bg-white/20 backdrop-blur-sm w-10 h-10 flex items-center justify-center rounded-xl mb-4 border border-white/10">
              <i class="fa-solid fa-rocket text-white text-sm"></i>
            </div>
            <div class="inline-flex items-center gap-1.5 bg-emerald-400/20 border border-emerald-300/20 px-2.5 py-1 rounded-full mb-3">
              <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
              <span class="text-[10px] font-bold text-emerald-200 uppercase tracking-widest">Offre limitée</span>
            </div>
            <h3 class="text-lg font-black leading-tight mb-2">Boostez votre carrière sportive</h3>
            <p class="text-xs text-emerald-100/80 font-medium mb-5 leading-relaxed">
              Accédez aux offres exclusives des clubs de Ligue 1 et connectez-vous avec des agents certifiés.
            </p>
            <button class="bg-white text-[#064E3B] w-full py-3 rounded-xl font-black text-xs hover:bg-emerald-50 transition-all active:scale-95 shadow-sm flex items-center justify-center gap-2">
              <i class="fa-solid fa-crown text-amber-500"></i>
              Découvrir SportLink Pro
            </button>
          </div>

        </div>

        <div class="px-2">
          <p class="text-[10px] text-gray-400 leading-relaxed">
            <a href="#" class="hover:text-emerald-600 transition-colors">Confidentialité</a> ·
            <a href="#" class="hover:text-emerald-600 transition-colors">Conditions</a> ·
            <a href="#" class="hover:text-emerald-600 transition-colors">Aide</a> ·
            <a href="#" class="hover:text-emerald-600 transition-colors">À propos</a>
          </p>
          <p class="text-[10px] text-gray-300 mt-1">SportLink © 2025</p>
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
const error = ref('')

// Use computed property to stay reactive with store changes
const posts = computed(() => postStore.posts)

const trendingTopics = [
  { category: 'Football · Ligue 1', tag: '#TransferWindow', count: '2.4k' },
  { category: 'Basketball', tag: '#ProA2025', count: '1.8k' },
  { category: 'Rugby', tag: '#Top14Final', count: '956' },
]

const reloadPosts = async () => {
  error.value = ''
  loading.value = true
  try {
    await postStore.fetchPosts()
  } catch (err) {
    error.value = err.message || 'Impossible de charger les publications.'
  } finally {
    loading.value = false
  }
}

onMounted(reloadPosts)
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.4s ease-out both;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* TransitionGroup animations for posts */
.post-list-enter-active,
.post-list-leave-active {
  transition: all 0.35s ease;
}

.post-list-enter-from {
  opacity: 0;
  transform: translateY(16px);
}

.post-list-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>

