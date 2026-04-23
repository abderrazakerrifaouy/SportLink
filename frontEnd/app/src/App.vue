<script setup>
import { RouterView } from 'vue-router'
import { useAuthStore } from './stores/AuthStore';
import { onMounted } from 'vue';
const auth = useAuthStore();
onMounted(() => {
  auth.fetchUser().catch((error) => {
    console.error('Initial auth bootstrap failed:', error)
  })
})
</script>

<template>
  <RouterView />
</template>

