<template>
  <div class="p-6 border mt-4 rounded-lg shadow-lg bg-white">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">
      Export / Import Excel untuk {{ resource }}
    </h3>
    
    <!-- Input untuk menentukan kolom yang akan diexport secara dinamis -->
    <div class="mb-4">
      <label class="block text-sm text-gray-600 mb-1">
        Kolom Export (pisahkan dengan koma):
      </label>
      <input 
        v-model="exportFields" 
        type="text" 
        placeholder="id,name" 
        class="w-full border border-gray-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
      >
    </div>
    
    <!-- Tombol Export -->
    <div class="mb-4 flex items-center">
      <button 
        @click="exportExcel" 
        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md mr-4 transition"
      >
        Export Excel
      </button>
    </div>
    
    <!-- Form Import -->
    <form 
      @submit.prevent="importExcel" 
      enctype="multipart/form-data" 
      class="mb-4 flex flex-wrap items-center gap-4"
    >
      <input 
        type="file" 
        @change="onFileChange" 
        accept=".xlsx,.xls" 
        class="border border-gray-300 p-2 rounded-md"
      >
      <button 
        type="submit" 
        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition"
      >
        Import Excel
      </button>
    </form>
    
    <!-- Loading Indicator -->
    <div v-if="isLoading" class="mb-2 text-blue-500 font-medium">
      Memproses... harap tunggu.
    </div>
    
    <!-- Output Message -->
    <div v-if="message" class="mt-2 p-3 border rounded bg-gray-100 text-gray-700">
      {{ message }}
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: "ExcelImportExport",
  props: {
    resource: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      file: null,
      message: "",
      exportFields: "id,name", // default dynamic field input
      isLoading: false
    };
  },
  methods: {
    onFileChange(event) {
      this.file = event.target.files[0];
    },
    exportExcel() {
      this.isLoading = true;
      // Kirim request export dengan parameter resource dan field dinamis
      axios.post("/excel/export", { table: this.resource, fields: this.exportFields })
        .then(response => {
          this.message = response.data.message;
          this.isLoading = false;
        })
        .catch(error => {
          console.error(error);
          this.message = "Export gagal.";
          this.isLoading = false;
        });
    },
    importExcel() {
      if (!this.file) {
        alert("Pilih file Excel terlebih dahulu.");
        return;
      }
      this.isLoading = true;
      let formData = new FormData();
      formData.append("table", this.resource);
      formData.append("file", this.file);
      
      axios.post("/excel/import", formData, {
        headers: { "Content-Type": "multipart/form-data" }
      })
      .then(response => {
        this.message = response.data.message;
        this.isLoading = false;
      })
      .catch(error => {
        console.error(error);
        this.message = "Import gagal.";
        this.isLoading = false;
      });
    }
  }
};
</script>

<style scoped>
/* Jika diperlukan penyesuaian tambahan untuk tampilan */
@media (max-width: 640px) {
  .p-6 {
    padding: 1rem;
  }
}
</style>
