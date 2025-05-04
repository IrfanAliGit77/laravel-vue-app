<!-- resources/js/Pages/RoleManagement/RoleForm.vue -->
<script setup>
const props = defineProps({
  name: String,
  errors: Object,
  isEditing: Boolean,
  processing: Boolean,
});

const emit = defineEmits(['submit', 'cancel']);

const model = defineModel('name');
</script>

<template>
  <form @submit.prevent="emit('submit')" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
    <input
      v-model="model"
      type="text"
      placeholder="Nama Role"
      class="form-control w-full sm:w-auto flex-1"
    />
    <div v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</div>

    <button
      type="submit"
      :disabled="processing"
      class="btn btn-primary"
    >
      {{ isEditing ? 'Update' : 'Create' }}
    </button>
    <button
      v-if="isEditing"
      type="button"
      class="btn btn-secondary"
      @click="emit('cancel')"
    >
      Cancel
    </button>
  </form>
</template>
