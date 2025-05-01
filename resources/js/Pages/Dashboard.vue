<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
    <p>Welcome, {{ user.name }}!</p>
    <!-- Additional dashboard content goes here -->
  </div>
</template>

<script>
export default {
  props: {
    user: Object
  }
}
</script>

<style scoped>
/* Responsive styling if needed */
</style>
