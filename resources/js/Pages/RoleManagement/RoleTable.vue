<!-- resources/js/Pages/RoleManagement/ConfirmDialog.vue -->
<script setup>
const emit = defineEmits(['confirm', 'cancel']);
</script>

<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6 text-center space-y-4">
      <h2 class="text-lg font-bold">Konfirmasi Hapus</h2>
      <p>Yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
      <div class="flex justify-center gap-4">
        <button
          class="btn btn-danger"
          @click="emit('confirm')"
        >
          Ya, Hapus
        </button>
        <button
          class="btn btn-secondary"
          @click="emit('cancel')"
        >
          Batal
        </button>
      </div>
    </div>
  </div>
</template>
