<script setup>
import { onMounted } from 'vue'
const props = defineProps({ users: Object })
onMounted(() => {
  console.log('Users data:', props.users);
})
</script>

<template>
  <div class="container mx-auto p-4">
    <!-- Judul Halaman -->
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Manajemen User</h1>
    
    <!-- Form untuk menambahkan user baru -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-700 mb-4">Tambah User Baru</h2>
      <form @submit.prevent="createUser" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input
          v-model="form.name"
          type="text"
          placeholder="Nama"
          required
          class="border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
        />
        <input
          v-model="form.email"
          type="email"
          placeholder="Email"
          required
          class="border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
        />
        <input
          v-model="form.password"
          type="password"
          placeholder="Password"
          required
          class="border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
        />
        <select
          v-model="form.role_id"
          required
          class="border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
        >
          <option disabled value="">Pilih Role</option>
          <option v-for="role in roles" :key="role.id" :value="role.id">
            {{ role.name }}
          </option>
        </select>
        <div class="md:col-span-2 text-right">
          <button 
            type="submit" 
            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition"
          >
            Tambah User
          </button>
        </div>
      </form>
    </div>
    
    <!-- Input pencarian untuk filtering -->
    <div class="mb-4">
      <input
        v-model="searchTerm"
        type="text"
        placeholder="Cari user..."
        class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
        @input="filterUsers"
      />
    </div>
    
    <!-- Tabel Daftar User -->
    <div class="bg-white shadow rounded-lg overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="(user, index) in filteredUsers"
            :key="user.id"
          >
            <td class="px-6 py-4 whitespace-nowrap">{{ index + 1 }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ user.role ? user.role.name : 'No Role' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
              <button
                @click="editUser(user)"
                class="px-3 py-1 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition mr-2"
              >
                Edit
              </button>
              <button
                @click="deleteUser(user.id)"
                class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 transition"
              >
                Hapus
              </button>
            </td>
          </tr>
          <!-- Tampilkan pesan jika tidak ada user -->
          <tr v-if="filteredUsers.length === 0">
            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
              Tidak ada user ditemukan.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "UserManagement",
  data() {
    return {
      users: [],
      roles: [],
      searchTerm: "",
      filteredUsers: [],
      form: {
        name: "",
        email: "",
        password: "",
        role_id: ""
      }
    };
  },
  methods: {
    fetchUsers() {
      axios
        .get("/users")
        .then((response) => {
          // Sesuaikan respons API Anda; jika menggunakan pagination bisa diubah
          this.users = response.data.data || response.data;
          this.filteredUsers = this.users;
        })
        .catch((error) => console.error(error));
    },
    fetchRoles() {
      axios
        .get("/roles")
        .then((response) => {
          this.roles = response.data.data || response.data;
        })
        .catch((error) => console.error(error));
    },
    createUser() {
      axios
        .post("/users", this.form)
        .then(() => {
          // Reset form setelah berhasil menyimpan
          this.form = { name: "", email: "", password: "", role_id: "" };
          this.fetchUsers();
        })
        .catch((error) => console.error(error));
    },
    editUser(user) {
      // Contoh edit sederhana menggunakan prompt
      const newName = prompt("Edit Nama User", user.name);
      if (newName && newName.trim() && newName !== user.name) {
        axios
          .put(`/users/${user.id}`, { name: newName })
          .then(() => this.fetchUsers())
          .catch((error) => console.error(error));
      }
    },
    deleteUser(userId) {
      if (confirm("Yakin hapus user ini?")) {
        axios
          .delete(`/users/${userId}`)
          .then(() => this.fetchUsers())
          .catch((error) => console.error(error));
      }
    },
    filterUsers() {
      if (!this.searchTerm.trim()) {
        this.filteredUsers = this.users;
      } else {
        const term = this.searchTerm.toLowerCase();
        this.filteredUsers = this.users.filter(
          (user) =>
            user.name.toLowerCase().includes(term) ||
            user.email.toLowerCase().includes(term) ||
            (user.role && user.role.name.toLowerCase().includes(term))
        );
      }
    }
  },
  mounted() {
    this.fetchUsers();
    this.fetchRoles();
  }
};
</script>

<style scoped>
/* Responsif: Penyesuaian padding dan font-size pada layar kecil */
@media (max-width: 640px) {
  .container {
    padding: 1rem;
  }
  table {
    font-size: 0.875rem;
  }
}
</style>
