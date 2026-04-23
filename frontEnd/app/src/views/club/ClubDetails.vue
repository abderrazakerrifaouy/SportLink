<template>
  <div class="space-y-6">
    <div v-if="loading" class="flex justify-center py-14">
      <div class="h-10 w-10 animate-spin rounded-full border-b-2 border-indigo-600"></div>
    </div>

    <div v-else-if="error" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
      {{ error }}
    </div>

    <div v-else-if="club" class="space-y-6">
      <div class="overflow-hidden rounded-3xl bg-white shadow-sm border border-gray-100">
        <div class="bg-linear-to-r from-indigo-600 via-blue-600 to-cyan-500 px-6 py-8 text-white">
          <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex items-start gap-4">
              <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-3xl font-black backdrop-blur-sm">
                {{ club.nomClub?.charAt(0) || 'C' }}
              </div>
              <div>
                <h1 class="text-3xl font-black tracking-tight">{{ club.nomClub }}</h1>
                <p class="mt-2 max-w-3xl text-sm text-white/85">{{ club.description || 'No description provided.' }}</p>
              </div>
            </div>

            <button
              type="button"
              @click="router.push({ name: 'Clubs' })"
              class="inline-flex items-center self-start rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/25"
            >
              <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
              Back to clubs
            </button>
          </div>

          <div class="mt-6 flex flex-wrap gap-2 text-xs font-bold uppercase tracking-wider text-white/90">
            <span class="rounded-full bg-white/15 px-3 py-1">{{ club.joueurs?.length || 0 }} Players</span>
            <span class="rounded-full bg-white/15 px-3 py-1">{{ club.titres?.length || 0 }} Titles</span>
            <span class="rounded-full bg-white/15 px-3 py-1">{{ club.posts?.length || 0 }} Posts</span>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-6 p-6 xl:grid-cols-3">
          <section class="rounded-2xl border border-gray-100 bg-gray-50/60 p-5">
            <h2 class="text-sm font-black uppercase tracking-wider text-gray-500">Club Info</h2>
            <dl class="mt-4 space-y-3 text-sm">
              <div class="flex items-start justify-between gap-4">
                <dt class="text-gray-500">Admin</dt>
                <dd class="text-right font-semibold text-gray-900">{{ club.user?.name || 'Unknown' }}</dd>
              </div>
              <div class="flex items-start justify-between gap-4">
                <dt class="text-gray-500">Email</dt>
                <dd class="text-right font-semibold text-gray-900">{{ club.user?.email || 'No email' }}</dd>
              </div>
              <div class="flex items-start justify-between gap-4">
                <dt class="text-gray-500">School</dt>
                <dd class="text-right font-semibold text-gray-900">{{ club.ecole || 'Not set' }}</dd>
              </div>
              <div class="flex items-start justify-between gap-4">
                <dt class="text-gray-500">Tactics</dt>
                <dd class="text-right font-semibold text-gray-900">{{ club.tactique || 'Not set' }}</dd>
              </div>
              <div class="flex items-start justify-between gap-4">
                <dt class="text-gray-500">Management</dt>
                <dd class="text-right font-semibold text-gray-900">{{ club.gestion || 'Not set' }}</dd>
              </div>
            </dl>
          </section>

          <section class="rounded-2xl border border-gray-100 p-5">
            <div class="mb-4 flex items-center justify-between gap-3">
              <h2 class="text-sm font-black uppercase tracking-wider text-gray-500">Players</h2>
              <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700">{{ club.joueurs?.length || 0 }}</span>
            </div>

            <div v-if="club.joueurs?.length" class="space-y-3">
              <div
                v-for="player in club.joueurs"
                :key="player.id"
                class="flex items-center gap-3 rounded-2xl bg-gray-50 px-3 py-2"
              >
                <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-indigo-100 text-sm font-black text-indigo-700">
                  <img v-if="player.user?.profile_photo_path" :src="player.user.profile_photo_path" alt="" class="h-full w-full object-cover" />
                  <span v-else>{{ player.user?.name?.charAt(0) || 'P' }}</span>
                </div>
                <div class="min-w-0">
                  <p class="truncate text-sm font-semibold text-gray-900">{{ player.user?.name }}</p>
                  <p class="truncate text-xs text-gray-500">{{ player.user?.email }}</p>
                </div>
              </div>
            </div>

            <div v-else class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-8 text-center text-sm text-gray-500">
              No players yet.
            </div>
          </section>

          <section class="rounded-2xl border border-gray-100 p-5">
            <div class="mb-4 flex items-center justify-between gap-3">
              <h2 class="text-sm font-black uppercase tracking-wider text-gray-500">Titles</h2>
              <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">{{ club.titres?.length || 0 }}</span>
            </div>

            <div v-if="club.titres?.length" class="space-y-3">
              <div
                v-for="title in club.titres"
                :key="title.id"
                class="rounded-2xl border border-amber-100 bg-amber-50/60 p-3"
              >
                <p class="font-semibold text-gray-900">{{ title.nomTitre || title.name || 'Title' }}</p>
                <p v-if="title.description" class="mt-1 text-sm text-gray-600">{{ title.description }}</p>
              </div>
            </div>

            <div v-else class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-8 text-center text-sm text-gray-500">
              No titles yet.
            </div>
          </section>
        </div>

        <div class="border-t border-gray-100 px-6 py-6">
          <div class="mb-4 flex items-center justify-between gap-3">
            <div>
              <h2 class="text-sm font-black uppercase tracking-wider text-gray-500">Posts by this Club Admin</h2>
              <p class="mt-1 text-sm text-gray-500">Posts published by {{ club.user?.name || 'this admin' }}.</p>
            </div>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">{{ club.posts?.length || 0 }}</span>
          </div>

          <div v-if="club.posts?.length" class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <article
              v-for="post in club.posts"
              :key="post.id"
              class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm"
            >
              <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-gray-100 text-sm font-black text-gray-500">
                    <img v-if="post.author?.profileImage" :src="post.author.profileImage" alt="" class="h-full w-full object-cover" />
                    <span v-else>{{ post.author?.name?.charAt(0) || 'A' }}</span>
                  </div>
                  <div>
                    <p class="text-sm font-semibold text-gray-900">{{ post.author?.name || club.user?.name || 'Club Admin' }}</p>
                    <p class="text-xs text-gray-500">{{ post.created_at }}</p>
                  </div>
                </div>
                <span class="rounded-full bg-gray-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                  {{ post.reactions_summary?.total || 0 }} reactions
                </span>
              </div>

              <p class="mt-4 whitespace-pre-line text-sm leading-6 text-gray-700">
                {{ post.content }}
              </p>

              <div class="mt-4 flex flex-wrap items-center gap-2 text-xs font-semibold text-gray-500">
                <span class="rounded-full bg-gray-50 px-2.5 py-1">{{ post.comments_count || 0 }} comments</span>
                <span class="rounded-full bg-gray-50 px-2.5 py-1">{{ post.media?.length || 0 }} media items</span>
              </div>
            </article>
          </div>

          <div v-else class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-4 py-10 text-center text-sm text-gray-500">
            No posts yet.
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import clubAdminService from '@/services/cloubAdminService'

const route = useRoute()
const router = useRouter()
const club = ref(null)
const loading = ref(true)
const error = ref(null)

const fetchClubDetails = async () => {
  loading.value = true
  error.value = null

  try {
    const response = await clubAdminService.getClubAdminById(route.params.id)
    club.value = response.data
  } catch (err) {
    error.value = 'Failed to load club information'
    console.error(err)
  } finally {
    loading.value = false
  }
}

onMounted(fetchClubDetails)
</script>
