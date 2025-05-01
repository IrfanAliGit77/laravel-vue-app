<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Role Management</h1>
    <form @submit.prevent="createRole" class="mb-4">
      <input v-model="newRole" type="text" placeholder="New Role" class="border p-2 mr-2">
      <button type="submit" class="bg-blue-500 text-white p-2">Add Role</button>
    </form>
    <ul class="mt-4">
      <li v-for="role in roles.data" :key="role.id" class="flex justify-between p-2 border-b">
        <span>{{ role.name }}</span>
        <div>
          <button @click="editRole(role)" class="bg-yellow-500 text-white p-1 mr-2">Edit</button>
          <button @click="deleteRole(role.id)" class="bg-red-500 text-white p-1">Delete</button>
        </div>
      </li>
    </ul>
    <pagination :data="roles" @pagination-change-page="fetchRoles"></pagination>
  </div>
</template>

<script>
import axios from 'axios';
import Pagination from '@/Components/Pagination.vue';

export default {
  components: { Pagination },
  data() {
    return {
      newRole: '',
      roles: { data: [] }
    }
  },
  mounted() {
    this.fetchRoles();
  },
  methods: {
    fetchRoles(page = 1) {
      axios.get(`/roles?page=${page}`).then(response => {
        this.roles = response.data;
      });
    },
    createRole() {
      axios.post('/roles', { name: this.newRole }).then(() => {
        this.newRole = '';
        this.fetchRoles();
      }).catch(error => console.error(error.response.data));
    },
    editRole(role) {
      const newName = prompt("Edit Role Name", role.name);
      if(newName) {
        axios.put(`/roles/${role.id}`, { name: newName }).then(() => {
          this.fetchRoles();
        });
      }
    },
    deleteRole(id) {
      if(confirm("Are you sure?")) {
        axios.delete(`/roles/${id}`).then(() => {
          this.fetchRoles();
        });
      }
    }
  }
}
</script>

<style scoped>
/* Additional responsive design as needed */
</style>
