<template>
  <MainLayout>
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">

      <div class="xl:col-span-8 space-y-8">
        <CreatePost />
        <div v-for="post in posts" :key="post.id" class="">
          <PostComponent :post="post" />
          <i class="fa-solid fa-star absolute -right-4 -bottom-4 text-gray-200 text-8xl rotate-12"></i>
        </div>
      </div>

      <aside class="xl:col-span-4 hidden xl:flex flex-col gap-8">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-md">
          <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Suggestions</h3>
          <div class="space-y-6">
            <SuggestionItem name="Sarah L." job="Coach" img="https://i.pravatar.cc/150?u=sarah" />
            <SuggestionItem name="David Kim" job="Agent" img="https://i.pravatar.cc/150?u=david" />
          </div>
        </div>

        <div class="bg-[#064E3B] rounded-xl p-6 text-white relative overflow-hidden group">
          <div class="relative z-10">
            <h3 class="text-lg font-bold leading-tight mb-2">Boostez votre carrière</h3>
            <p class="text-xs text-green-100 font-light mb-4">Passez au compte Pro pour accéder aux offres exclusives des clubs de Ligue 1.</p>
            <button class="bg-white text-[#064E3B] w-full py-2 rounded font-black text-[11px] hover:bg-green-50 transition">Découvrir SportLink Pro</button>
          </div>
          <i class="fa-solid fa-star absolute -right-4 -bottom-4 text-white opacity-10 text-8xl rotate-12 group-hover:scale-110 transition-transform"></i>
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
import { ref, onMounted } from 'vue'
import { usePostStore } from '@/stores/PostStore';

const postStore = usePostStore()
const posts = ref([])

onMounted(async () => {
  await postStore.fetchPosts();


  posts.value = postStore.posts;
  console.log('المنشورات بعد الجلب:', posts.value);
});



</script>
