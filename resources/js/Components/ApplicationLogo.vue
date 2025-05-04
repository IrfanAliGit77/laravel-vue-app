<script setup>
// Import logo.jpg — sesuaikan path relatif dari file ini
import logo from '@/img/logo.jpg'
</script>

<template>
  <div class="flex items-center">
    <!-- Logo gambar -->
    <img
      :src="logo"
      alt="Logo"
      class="h-12 w-12 object-contain rounded-full shadow-md"
    />
  </div>
</template>

<style scoped>
img {
  /* border: 2px solid #4B5563; */
}
</style>
