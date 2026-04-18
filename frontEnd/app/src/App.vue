<script setup>
    import {
        RouterView
    } from 'vue-router'
    import {
        useAuthStore
    } from './stores/AuthStore';

   
    const store = useAuthStore()
    store.fetchUser()
</script>

<template>
    <RouterView />
</template>
