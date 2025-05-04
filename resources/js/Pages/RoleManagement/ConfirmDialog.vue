<script setup>
defineProps({
  title: String,
  message: String,
});
defineEmits(['confirm', 'cancel']);
</script>

<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="rounded-lg bg-white p-6 shadow-lg w-full max-w-md">
      <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ title }}</h2>
      <p class="text-gray-600 mb-6">{{ message }}</p>
      <div class="flex justify-end space-x-4">
        <button
          @click="$emit('cancel')"
          class="rounded bg-gray-300 px-4 py-2 text-gray-800 hover:bg-gray-400 transition"
        >
          Batal
        </button>
        <button
          @click="$emit('confirm')"
          class="rounded bg-red-500 px-4 py-2 text-white hover:bg-red-600 transition"
        >
          Hapus
        </button>
      </div>
    </div>
  </div>
</template>
