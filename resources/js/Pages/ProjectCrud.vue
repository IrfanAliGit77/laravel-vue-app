<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Projects</h1>
    <button @click="showCreate = !showCreate" class="bg-green-500 text-white p-2 mb-4">New Project</button>
    <div v-if="showCreate" class="mb-4">
      <form @submit.prevent="createProject" enctype="multipart/form-data">
        <input v-model="form.name" type="text" placeholder="Project Name" class="border p-2 mb-2 block">
        <textarea v-model="form.description" placeholder="Description" class="border p-2 mb-2 block"></textarea>
        <input v-model="form.due_date" type="datetime-local" class="border p-2 mb-2 block">
        <input type="file" @change="onFileChange" accept="application/pdf" class="mb-2 block">
        <button type="submit" class="bg-blue-500 text-white p-2">Create</button>
      </form>
    </div>
    <div>
      <div v-for="project in projects.data" :key="project.id" class="p-4 border rounded mb-2">
        <h2 class="text-xl font-semibold">{{ project.name }}</h2>
        <p>{{ project.description }}</p>
        <div>
          <button @click="editProject(project)" class="bg-yellow-500 text-white p-1 mr-2">Edit</button>
          <button @click="deleteProject(project.id)" class="bg-red-500 text-white p-1">Delete</button>
        </div>
      </div>
    </div>
    <pagination :data="projects" @pagination-change-page="fetchProjects"></pagination>
  </div>
</template>

<script>
import axios from 'axios';
import Pagination from '@/Components/Pagination.vue';

export default {
  components: { Pagination },
  data() {
    return {
      projects: { data: [] },
      showCreate: false,
      form: {
        name: '',
        description: '',
        due_date: '',
        document: null,
        metadata: '{}',
        status: true,
      }
    }
  },
  mounted() {
    this.fetchProjects();
  },
  methods: {
    fetchProjects(page = 1) {
      axios.get(`/projects?page=${page}`).then(response => {
        this.projects = response.data;
      });
    },
    onFileChange(event) {
      this.form.document = event.target.files[0];
    },
    createProject() {
      let formData = new FormData();
      Object.keys(this.form).forEach(key => {
        formData.append(key, this.form[key]);
      });
      axios.post('/projects', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      }).then(() => {
        this.showCreate = false;
        this.form = { name: '', description: '', due_date: '', document: null, metadata: '{}', status: true };
        this.fetchProjects();
      }).catch(error => console.error(error));
    },
    editProject(project) {
      // Implement edit logic similar to create
    },
    deleteProject(id) {
      if(confirm("Are you sure to delete?")) {
        axios.delete(`/projects/${id}`).then(() => {
          this.fetchProjects();
        });
      }
    }
  }
}
</script>

<style scoped>
/* Responsive design adjustments */
</style>
