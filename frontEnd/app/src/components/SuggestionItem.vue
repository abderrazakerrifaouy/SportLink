<template>
  <div class="flex items-center justify-between group p-2 hover:bg-gray-50 rounded-xl transition-colors">
    <div class="flex items-center gap-4">
      <img :src="img" class="h-14 w-14 rounded-xl object-cover shadow-sm border border-gray-100" :alt="name">

      <div>
        <h4 class="text-base font-extrabold text-gray-800 leading-tight group-hover:text-[#064E3B] transition-colors">
          {{ name }}
        </h4>
        <p class="text-xs text-gray-500 font-medium mt-1">{{ job }}</p>
      </div>
    </div>

    <button class="text-xs border-2 border-gray-200 px-5 py-2 rounded-lg font-black text-gray-700 hover:bg-[#064E3B] hover:text-white hover:border-[#064E3B] transition-all active:scale-95">
      Suivre
    </button>
  </div>
</template>

<script setup>
defineProps(['name', 'job', 'img'])
</script>
