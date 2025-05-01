<template>
  <div class="flex justify-center mt-4">
    <button
      v-for="page in data.last_page"
      :key="page"
      @click="$emit('pagination-change-page', page)"
      :class="{
        'bg-blue-500 text-white px-3 py-1 m-1': page === data.current_page,
        'bg-gray-200 px-3 py-1 m-1': page !== data.current_page
      }"
    >
      {{ page }}
    </button>
  </div>
</template>

<script>
export default {
  props: ['data']
}
</script>

<style scoped>
</style>
