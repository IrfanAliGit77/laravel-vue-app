<!-- resources/js/Pages/RoleManagement/index.vue -->
<script setup>
import { ref, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { route } from 'ziggy-js';
import RoleForm from './RoleForm.vue';
import RoleTable from './RoleTable.vue';
import ConfirmDialog from './ConfirmDialog.vue';

const props = defineProps({
  roles: Object,
  flash: Object,
});

const form = useForm({ id: null, name: '' });
const isEditing = ref(false);
const showConfirm = ref(false);
const toDeleteId = ref(null);
const searchQuery = ref('');
const selectedLetter = ref('');

function submitForm() {
  if (isEditing.value) {
    form.put(route('roles.update', form.id), {
      preserveScroll: true,
      onSuccess: () => {
        isEditing.value = false;
        form.reset();
      },
    });
  } else {
    form.post(route('roles.store'), {
      preserveScroll: true,
      onSuccess: () => form.reset(),
    });
  }
}

function editRole(role) {
  isEditing.value = true;
  form.id = role.id;
  form.name = role.name;
}

function confirmDelete(id) {
  toDeleteId.value = id;
  showConfirm.value = true;
}

function deleteRole() {
  router.delete(route('roles.destroy', toDeleteId.value), {
    preserveScroll: true,
    onSuccess: () => {
      showConfirm.value = false;
      toDeleteId.value = null;
    },
  });
}

const filteredRoles = computed(() => {
  let roles = props.roles.data;

  if (searchQuery.value) {
    roles = roles.filter(role =>
      role.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  }

  if (selectedLetter.value) {
    roles = roles.filter(role =>
      role.name.toLowerCase().startsWith(selectedLetter.value.toLowerCase())
    );
  }

  return roles;
});
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Role Management" />
    <div class="container mx-auto px-4 py-6 space-y-6">

      <div v-if="flash.success" class="alert alert-success shadow-md rounded-lg p-4 bg-green-100 text-green-800">
        {{ flash.success }}
      </div>

      <RoleForm
        v-model:name="form.name"
        :errors="form.errors"
        :isEditing="isEditing"
        :processing="form.processing"
        @submit="submitForm"
        @cancel="() => { isEditing = false; form.reset(); }"
      />

      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <input
          v-model="searchQuery"
          type="text"
          class="form-control w-full sm:w-1/2"
          placeholder="Cari Role..."
        />
        <select
          v-model="selectedLetter"
          class="form-select w-full sm:w-1/4"
        >
          <option value="">Semua Huruf</option>
          <option v-for="letter in 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'" :key="letter" :value="letter">
            {{ letter }}
          </option>
        </select>
      </div>

      <RoleTable
        :roles="filteredRoles"
        @edit="editRole"
        @delete="confirmDelete"
      />

      <ConfirmDialog
        v-if="showConfirm"
        @confirm="deleteRole"
        @cancel="() => showConfirm = false"
      />
    </div>
  </AuthenticatedLayout>
</template>
