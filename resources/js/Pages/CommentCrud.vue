<template>
  <div class="container mx-auto p-6">
    <!-- Judul Halaman -->
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Manajemen Comment</h1>

    <!-- Tombol Toggle Form Create -->
    <div class="flex justify-end mb-4">
      <button 
        @click="toggleCreate" 
        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition">
        {{ showCreate ? "Tutup Form" : "Comment Baru" }}
      </button>
    </div>

    <!-- Form Create Comment -->
    <div v-if="showCreate" class="bg-white shadow-lg rounded-lg p-6 mb-6">
      <h2 class="text-2xl font-semibold text-gray-700 mb-4">Buat Comment Baru</h2>
      <form @submit.prevent="createComment" class="space-y-4">
        <div>
          <label class="block text-gray-600 mb-1">Pilih Task</label>
          <select 
            v-model="form.task_id" 
            required 
            class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option disabled value="">Pilih Task</option>
            <option 
              v-for="task in tasks" 
              :key="task.id" 
              :value="task.id">
              {{ task.title }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-gray-600 mb-1">Tulis Comment</label>
          <textarea 
            v-model="form.comment" 
            placeholder="Tulis comment..." 
            required 
            class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
        </div>
        <div class="text-right">
          <button 
            type="submit" 
            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md transition">
            Buat Comment
          </button>
        </div>
      </form>
    </div>

    <!-- Pencarian / Filtering -->
    <div class="mb-6">
      <input 
        type="text" 
        v-model="searchTerm" 
        placeholder="Cari comment..." 
        class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" 
        @input="filterComments">
    </div>

    <!-- Daftar Comment -->
    <div class="space-y-6">
      <div 
        v-for="comment in filteredComments" 
        :key="comment.id" 
        class="bg-white shadow rounded-lg p-6">
        <div class="mb-2">
          <p class="text-gray-800 text-lg font-semibold">{{ comment.comment }}</p>
          <p class="text-gray-600">Task: <span class="font-medium">{{ comment.task.title }}</span></p>
        </div>
        <div class="flex flex-wrap gap-2">
          <button 
            @click="toggleAudit(comment.id)" 
            class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded-md transition">
            {{ showAudit[comment.id] ? 'Sembunyikan Audit' : 'Lihat Audit' }}
          </button>
          <button 
            @click="editComment(comment)" 
            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md transition">
            Edit
          </button>
          <button 
            @click="deleteComment(comment.id)" 
            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md transition">
            Hapus
          </button>
        </div>
        <!-- Audit Trail Section -->
        <div 
          v-if="showAudit[comment.id]" 
          class="mt-4 bg-gray-50 p-4 rounded border">
          <h4 class="text-lg font-bold text-gray-700 mb-2">Audit Trail</h4>
          <ul class="list-disc pl-5 space-y-1">
            <li 
              v-for="log in comment.audit_logs" 
              :key="log.id" 
              class="text-gray-600">
              {{ log.event }} - <span class="text-sm text-gray-500">{{ log.created_at }}</span>
            </li>
          </ul>
        </div>
      </div>
      <!-- Jika tidak ada komentar yang ditemukan -->
      <div v-if="filteredComments.length === 0" class="text-center text-gray-500">
        Tidak ada comment ditemukan.
      </div>
    </div>

    <!-- Komponen Export & Import Excel untuk Comment -->
    <div class="mt-8">
      <excel-import-export resource="comments"></excel-import-export>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import ExcelImportExport from "./ExcelImportExport.vue";

export default {
  name: "CommentCrud",
  components: { ExcelImportExport },
  data() {
    return {
      comments: [],
      tasks: [],
      filteredComments: [],
      searchTerm: "",
      showCreate: false,
      form: {
        task_id: "",
        comment: ""
      },
      showAudit: {}
    };
  },
  methods: {
    fetchComments() {
      axios.get("/comments")
        .then((response) => {
          this.comments = response.data.data || response.data;
          this.filteredComments = this.comments;
        })
        .catch((error) => console.error("Error fetching comments:", error));
    },
    fetchTasks() {
      axios.get("/tasks")
        .then((response) => {
          this.tasks = response.data.data || response.data;
        })
        .catch((error) => console.error("Error fetching tasks:", error));
    },
    filterComments() {
      if (!this.searchTerm.trim()) {
        this.filteredComments = this.comments;
      } else {
        const term = this.searchTerm.toLowerCase();
        this.filteredComments = this.comments.filter(comment =>
          comment.comment.toLowerCase().includes(term)
        );
      }
    },
    createComment() {
      axios.post("/comments", this.form)
        .then(() => {
          this.form = { task_id: "", comment: "" };
          this.showCreate = false;
          this.fetchComments();
        })
        .catch((error) => console.error("Error creating comment:", error));
    },
    editComment(comment) {
      const newComment = prompt("Edit comment", comment.comment);
      if (newComment && newComment.trim() !== comment.comment) {
        axios.put(`/comments/${comment.id}`, { comment: newComment })
          .then(() => this.fetchComments())
          .catch((error) => console.error("Error editing comment:", error));
      }
    },
    deleteComment(commentId) {
      if (confirm("Yakin hapus comment ini?")) {
        axios.delete(`/comments/${commentId}`)
          .then(() => this.fetchComments())
          .catch((error) => console.error("Error deleting comment:", error));
      }
    },
    toggleAudit(commentId) {
      if (!this.showAudit[commentId]) {
        axios.get(`/comments/${commentId}`)
          .then((response) => {
            this.comments = this.comments.map(comment => {
              if (comment.id === commentId) {
                comment.audit_logs = response.data.audit_logs;
              }
              return comment;
            });
            this.$set(this.showAudit, commentId, true);
          })
          .catch((error) => console.error("Error fetching audit logs:", error));
      } else {
        this.$set(this.showAudit, commentId, !this.showAudit[commentId]);
      }
    },
    toggleCreate() {
      this.showCreate = !this.showCreate;
    }
  },
  mounted() {
    this.fetchComments();
    this.fetchTasks();
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
