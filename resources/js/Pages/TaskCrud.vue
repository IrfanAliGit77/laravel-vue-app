<template>
  <div class="container mx-auto p-4">
    <!-- Judul Halaman -->
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Manajemen Task</h2>

    <!-- Tombol untuk Toggle Form Create -->
    <div class="flex justify-end mb-4">
      <button 
        @click="toggleCreate"
        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition">
        {{ showCreate ? "Tutup Form" : "Task Baru" }}
      </button>
    </div>

    <!-- Form Create Task -->
    <div v-if="showCreate" class="bg-white shadow-lg rounded-lg p-6 mb-6">
      <h3 class="text-2xl font-semibold text-gray-700 mb-4">Buat Task Baru</h3>
      <form @submit.prevent="createTask" class="space-y-4">
        <div>
          <label class="block text-gray-600 mb-1">Pilih Project</label>
          <select v-model="form.project_id" required class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option disabled value="">Pilih Project</option>
            <option v-for="project in projects" :key="project.id" :value="project.id">
              {{ project.name }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-gray-600 mb-1">Judul Task</label>
          <input
            v-model="form.title"
            type="text"
            placeholder="Judul Task"
            required
            class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
          />
        </div>
        <div>
          <label class="block text-gray-600 mb-1">Due Date</label>
          <input
            v-model="form.due_date"
            type="datetime-local"
            required
            class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
          />
        </div>
        <div>
          <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md transition">
            Buat Task
          </button>
        </div>
      </form>
    </div>

    <!-- Pencarian / Filtering -->
    <div class="mb-4">
      <input v-model="searchTerm" type="text" placeholder="Cari task..." class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" @input="filterTasks"/>
    </div>

    <!-- Daftar Task -->
    <div class="space-y-6">
      <div v-for="task in filteredTasks" :key="task.id" class="bg-white shadow rounded-lg p-6">
        <div class="flex flex-col md:flex-row justify-between items-start gap-4">
          <div>
            <h3 class="text-xl font-semibold text-gray-800">{{ task.title }}</h3>
            <p class="text-gray-600 mt-1">Due: <span class="font-medium">{{ task.due_date }}</span></p>
            <p class="text-gray-600 mt-1">Project: <span class="font-medium">{{ task.project.name }}</span></p>
          </div>
          <div class="flex space-x-2">
            <button @click="toggleAudit(task.id)" class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded-md transition">
              {{ showAudit[task.id] ? 'Sembunyikan Audit' : 'Lihat Audit' }}
            </button>
            <button @click="editTask(task)" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md transition">
              Edit
            </button>
            <button @click="deleteTask(task.id)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md transition">
              Hapus
            </button>
          </div>
        </div>
        <!-- Audit Trail Section -->
        <div v-if="showAudit[task.id]" class="mt-4 bg-gray-50 p-4 rounded border">
          <h4 class="text-lg font-bold text-gray-700 mb-2">Audit Trail</h4>
          <ul>
            <li v-for="log in task.audit_logs" :key="log.id" class="text-gray-600 border-b pb-1 mb-1">
              {{ log.event }} - <span class="text-sm text-gray-500">{{ log.created_at }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Komponen Export & Import Excel untuk Task -->
    <div class="mt-6">
      <excel-import-export resource="tasks"></excel-import-export>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import ExcelImportExport from "./ExcelImportExport.vue";

export default {
  name: "TaskCrud",
  components: { ExcelImportExport },
  data() {
    return {
      tasks: [],
      projects: [],
      filteredTasks: [],
      searchTerm: "",
      showCreate: false,
      form: {
        project_id: "",
        title: "",
        due_date: ""
      },
      showAudit: {}
    };
  },
  methods: {
    fetchTasks() {
      axios.get("/tasks")
        .then((response) => {
          // Mengasumsikan API mengembalikan data dalam bentuk: { data: [...] } atau array langsung
          this.tasks = response.data.data || response.data;
          this.filteredTasks = this.tasks;
        })
        .catch((error) => console.error(error));
    },
    fetchProjects() {
      axios.get("/projects")
        .then((response) => {
          this.projects = response.data.data || response.data;
        })
        .catch((error) => console.error(error));
    },
    filterTasks() {
      if (!this.searchTerm.trim()) {
        this.filteredTasks = this.tasks;
      } else {
        const term = this.searchTerm.toLowerCase();
        this.filteredTasks = this.tasks.filter(task =>
          task.title.toLowerCase().includes(term)
        );
      }
    },
    createTask() {
      axios.post("/tasks", this.form)
        .then(() => {
          this.form = { project_id: "", title: "", due_date: "" };
          this.showCreate = false;
          this.fetchTasks();
        })
        .catch((error) => console.error(error));
    },
    editTask(task) {
      const newTitle = prompt("Edit judul task", task.title);
      if (newTitle && newTitle.trim() !== task.title) {
        axios.put(`/tasks/${task.id}`, { title: newTitle })
          .then(() => this.fetchTasks())
          .catch((error) => console.error(error));
      }
    },
    deleteTask(taskId) {
      if (confirm("Yakin hapus task ini?")) {
        axios.delete(`/tasks/${taskId}`)
          .then(() => this.fetchTasks())
          .catch((error) => console.error(error));
      }
    },
    toggleAudit(taskId) {
      if (!this.showAudit[taskId]) {
        axios.get(`/tasks/${taskId}`)
          .then((response) => {
            // Perbarui audit_logs untuk task tertentu dari API
            this.tasks = this.tasks.map(task => {
              if (task.id === taskId) {
                task.audit_logs = response.data.audit_logs;
              }
              return task;
            });
            this.$set(this.showAudit, taskId, true);
          })
          .catch((error) => console.error(error));
      } else {
        this.$set(this.showAudit, taskId, !this.showAudit[taskId]);
      }
    },
    toggleCreate() {
      this.showCreate = !this.showCreate;
    }
  },
  mounted() {
    this.fetchTasks();
    this.fetchProjects();
  }
};
</script>

<style scoped>
@media (max-width: 640px) {
  .container {
    padding: 1rem;
  }
}
</style>
