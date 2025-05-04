<!-- resources/js/Pages/RoleManagement.vue -->
<script setup>
import { ref, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { route } from 'ziggy-js';

const props = defineProps({
  roles: Object,
  filters: {
    type: Object,
    default: () => ({}),
  },
  flash: {
    type: Object,
    default: () => ({}),
  },
});

const form = useForm({ id: null, name: '' });
const isEditing = ref(false);
const searchQuery = ref('');
const selectedLetter = ref('');

function handleSubmit() {
  if (isEditing.value) {
    form.put(route('roles.update', form.id), {
      preserveScroll: true,
      onSuccess: () => {
        form.reset();
        isEditing.value = false;
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

function deleteRole(id) {
  if (confirm('Yakin ingin menghapus role ini?')) {
    router.delete(route('roles.destroy', id), { preserveScroll: true });
  }
}

const filteredRoles = computed(() => {
  let filtered = props.roles.data;

  if (searchQuery.value) {
    filtered = filtered.filter((role) =>
      role.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  }

  if (selectedLetter.value) {
    filtered = filtered.filter((role) =>
      role.name.toLowerCase().startsWith(selectedLetter.value.toLowerCase())
    );
  }

  return filtered;
});
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Role Management" />

    <div class="container mx-auto px-4 py-6">
      <!-- Flash Message -->
      <div
        v-if="flash.success"
        class="mb-4 rounded-md bg-green-100 px-4 py-2 text-green-800"
      >
        {{ flash.success }}
      </div>

      <!-- Form Create/Update -->
      <form
        @submit.prevent="handleSubmit"
        class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center"
      >
        <div class="flex-1">
          <input
            v-model="form.name"
            type="text"
            placeholder="Nama Role"
            class="w-full rounded border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-200"
          />
          <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">
            {{ form.errors.name }}
          </div>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="rounded bg-indigo-600 px-6 py-2 text-white transition hover:bg-indigo-700 disabled:opacity-50"
        >
          {{ isEditing ? 'Update' : 'Create' }}
        </button>

        <button
          v-if="isEditing"
          type="button"
          @click="isEditing = false; form.reset()"
          class="rounded bg-gray-400 px-6 py-2 text-white hover:bg-gray-500"
        >
          Cancel
        </button>
      </form>

      <!-- Search and Filter -->
      <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari Role..."
          class="mb-2 w-full rounded border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-200 sm:mb-0 sm:w-1/2"
        />

        <select
          v-model="selectedLetter"
          class="w-full rounded border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-200 sm:w-1/4"
        >
          <option value="">Semua Huruf</option>
          <option
            v-for="letter in 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('')"
            :key="letter"
            :value="letter"
          >
            {{ letter }}
          </option>
        </select>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
            <tr>
              <th
                class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide"
              >
                No
              </th>
              <th
                class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wide"
              >
                Nama Role
              </th>
              <th
                class="px-6 py-3 text-center text-sm font-semibold uppercase tracking-wide"
              >
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 bg-white">
            <tr
              v-for="(role, idx) in filteredRoles"
              :key="role.id"
              :class="idx % 2 === 0 ? 'bg-gray-50' : ''"
            >
              <td class="px-6 py-4 text-sm text-gray-700">
                {{ idx + 1 }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-800">{{ role.name }}</td>
              <td class="px-6 py-4 text-center text-sm">
                <button
                  @click="editRole(role)"
                  class="mr-2 inline-block rounded bg-yellow-400 px-3 py-1 text-yellow-900 hover:bg-yellow-500 transition"
                >
                  Edit
                </button>
                <button
                  @click="deleteRole(role.id)"
                  class="inline-block rounded bg-red-500 px-3 py-1 text-white hover:bg-red-600 transition"
                >
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="filteredRoles.length === 0">
              <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                Tidak ada role ditemukan.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
@media (max-width: 640px) {
  table {
    font-size: 0.875rem;
  }
}
</style>
