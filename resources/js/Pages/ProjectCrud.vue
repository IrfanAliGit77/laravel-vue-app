<template>
  <div class="container mx-auto p-4">
    <!-- Judul Halaman -->
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Projects</h1>
    
    <!-- Tombol Toggle Form Create -->
    <div class="flex justify-end mb-4">
      <button 
        @click="toggleCreateForm" 
        class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">
        {{ showCreate ? 'Close Form' : 'New Project' }}
      </button>
    </div>

    <!-- Form Create Project -->
    <div v-if="showCreate" class="bg-white shadow rounded-lg p-6 mb-6">
      <h2 class="text-xl font-semibold text-gray-700 mb-4">Create New Project</h2>
      <form @submit.prevent="createProject" enctype="multipart/form-data">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-gray-600 mb-1">Project Name</label>
            <input 
              v-model="form.name" 
              type="text" 
              placeholder="Project Name" 
              required 
              class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
          </div>
          <div>
            <label class="block text-gray-600 mb-1">Due Date</label>
            <input 
              v-model="form.due_date" 
              type="datetime-local" 
              required 
              class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
          </div>
        </div>
        <div class="mt-4">
          <label class="block text-gray-600 mb-1">Description</label>
          <textarea 
            v-model="form.description" 
            placeholder="Description" 
            required 
            class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
        </div>
        <div class="mt-4 flex flex-col sm:flex-row gap-4">
          <div class="w-full sm:w-1/2">
            <label class="block text-gray-600 mb-1">Upload Document (PDF only, 100KB–500KB)</label>
            <input 
              type="file" 
              @change="onFileChange" 
              accept="application/pdf" 
              class="w-full">
          </div>
          <div class="w-full sm:w-1/2">
            <label class="block text-gray-600 mb-1">Metadata (JSON format)</label>
            <input 
              v-model="form.metadata" 
              type="text" 
              placeholder='{"key": "value"}' 
              class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
          </div>
        </div>
        <div class="mt-4">
          <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600 transition">
            Create
          </button>
        </div>
      </form>
    </div>

    <!-- Searching, Filtering, and Sorting Controls -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
      <input 
        type="text" 
        v-model="searchTerm" 
        placeholder="Search Project..." 
        class="w-full sm:w-1/2 border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
      <div class="flex items-center gap-2">
        <label class="text-gray-600">Sort by:</label>
        <select 
          v-model="sortBy" 
          @change="fetchProjects" 
          class="border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
          <option value="name">Name</option>
          <option value="due_date">Due Date</option>
        </select>
        <select 
          v-model="sortOrder" 
          @change="fetchProjects" 
          class="border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
          <option value="asc">Asc</option>
          <option value="desc">Desc</option>
        </select>
      </div>
    </div>

    <!-- List of Projects -->
    <div class="grid grid-cols-1 gap-6">
      <div 
        v-for="(project, index) in filteredProjects" 
        :key="project.id" 
        class="bg-white shadow rounded-lg p-4">
        <h2 class="text-xl font-semibold text-gray-800">{{ project.name }}</h2>
        <p class="text-gray-600 mt-2">{{ project.description }}</p>
        <p class="text-gray-500 mt-2">Due Date: {{ project.due_date }}</p>
        <div class="mt-4 flex gap-2 flex-wrap">
          <button 
            @click="editProject(project)" 
            class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 transition">
            Edit
          </button>
          <button 
            @click="deleteProject(project.id)" 
            class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition">
            Delete
          </button>
          <button 
            @click="toggleAudit(project.id)" 
            class="bg-indigo-500 text-white px-3 py-1 rounded-md hover:bg-indigo-600 transition">
            {{ showAudit[project.id] ? 'Hide Audit' : 'Show Audit' }}
          </button>
        </div>
        <!-- Audit Trail Section -->
        <div v-if="showAudit[project.id]" class="mt-4 bg-gray-50 p-3 rounded border">
          <h3 class="text-lg font-bold text-gray-700 mb-2">Audit Trail</h3>
          <ul>
            <li 
              v-for="log in project.audit_logs" 
              :key="log.id" 
              class="text-gray-600 border-b pb-1 mb-1">
              {{ log.event }} - <span class="text-sm text-gray-500">{{ log.created_at }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Pagination Component -->
    <div class="mt-6">
      <pagination :data="projects" @pagination-change-page="fetchProjects"></pagination>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Pagination from '@/Components/Pagination.vue';

export default {
  components: { Pagination },
  data() {
    return {
      projects: { data: [], current_page: 1, per_page: 10, total: 0 },
      showCreate: false,
      searchTerm: '',
      sortBy: 'name',
      sortOrder: 'asc',
      form: {
        name: '',
        description: '',
        due_date: '',
        document: null,
        metadata: '{}',
        status: true,
      },
      showAudit: {} // Menyimpan status tampilan audit per project id
    }
  },
  computed: {
    // Filter proyek berdasarkan search term
    filteredProjects() {
      let filtered = this.projects.data;
      if (this.searchTerm.trim()) {
        filtered = filtered.filter(project =>
          project.name.toLowerCase().includes(this.searchTerm.toLowerCase())
        );
      }
      return filtered;
    }
  },
  mounted() {
    this.fetchProjects();
  },
  methods: {
    toggleCreateForm() {
      this.showCreate = !this.showCreate;
    },
    fetchProjects(page = 1) {
      axios.get(`/projects?page=${page}`, {
        params: {
          search: this.searchTerm,
          sort_by: this.sortBy,
          sort_order: this.sortOrder
        }
      }).then(response => {
        this.projects = response.data;
      }).catch(error => {
        console.error(error);
      });
    },
    onFileChange(event) {
      const file = event.target.files[0];
      // Validasi ukuran file: minimal 100 KB dan maksimal 500 KB
      if (file && (file.size < 100 * 1024 || file.size > 500 * 1024)) {
        alert("File size must be between 100KB and 500KB.");
        event.target.value = null;
        return;
      }
      this.form.document = file;
    },
    createProject() {
      let formData = new FormData();
      for (let key in this.form) {
        formData.append(key, this.form[key]);
      }
      axios.post('/projects', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      }).then(() => {
        this.showCreate = false;
        // Reset form
        this.form = { name: '', description: '', due_date: '', document: null, metadata: '{}', status: true };
        this.fetchProjects();
      }).catch(error => {
        console.error(error);
      });
    },
    editProject(project) {
      // Contoh sederhana menggunakan prompt untuk edit nama
      const newName = prompt("Edit Project Name", project.name);
      if (newName && newName.trim() !== '') {
        axios.put(`/projects/${project.id}`, { name: newName })
          .then(() => {
            this.fetchProjects();
          }).catch(error => {
            console.error(error);
          });
      }
    },
    deleteProject(id) {
      if (confirm("Are you sure you want to delete this project?")) {
        axios.delete(`/projects/${id}`)
          .then(() => {
            this.fetchProjects();
          }).catch(error => {
            console.error(error);
          });
      }
    },
    toggleAudit(projectId) {
      // Jika belum ada data audit, fetch dari API dan kemudian toggle tampilan
      if (!this.showAudit[projectId]) {
        axios.get(`/projects/${projectId}`)
          .then(response => {
            // Asumsikan API mengembalikan audit logs sebagai response.data.audit_logs
            const updatedProjects = this.projects.data.map(project => {
              if (project.id === projectId) {
                project.audit_logs = response.data.audit_logs;
              }
              return project;
            });
            this.projects.data = updatedProjects;
            this.$set(this.showAudit, projectId, true);
          }).catch(error => {
            console.error(error);
          });
      } else {
        this.$set(this.showAudit, projectId, !this.showAudit[projectId]);
      }
    }
  }
}
</script>

<style scoped>
/* Penyesuaian responsif dan transisi */
@media (max-width: 640px) {
  .container {
    padding: 1rem;
  }
  table, .grid {
    font-size: 0.875rem;
  }
}
</style>
