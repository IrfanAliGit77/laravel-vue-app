<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import logo from '@/img/logo.jpg';

const props = defineProps({
  user: {
    type: Object,
    required: true,
    default: () => ({})
  }
});

const loginTime = ref('');

onMounted(() => {
  const now = new Date();
  loginTime.value = now.toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  });
});

const metrics = [
  { label: "Users", value: 120, color: "from-blue-500 to-blue-700", icon: "👤" },
  { label: "Projects", value: 15, color: "from-green-500 to-green-700", icon: "📁" },
  { label: "Tasks", value: 40, color: "from-purple-500 to-purple-700", icon: "✅" }
];

const activities = [
  "🕗 Logged in at 08:15 AM",
  "📊 Viewed Dashboard",
  "✏️ Updated Profile",
  "🚀 Created New Project"
];
</script>

<template>
  <AuthenticatedLayout :user="user">
    <Head title="Dashboard" />
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
          <img :src="logo" alt="Logo" class="h-12 w-12 rounded-full shadow-lg border border-gray-200" />
          <div>
            <h1 class="text-3xl font-extrabold text-gray-800">Dashboard</h1>
            <p class="text-gray-500 text-sm">Welcome back, <span class="font-semibold text-blue-600">{{ user.name }}</span> !</p>
            <p class="text-gray-500 text-sm">Logged in at: <span class="text-green-600 font-medium">{{ loginTime }}</span> !</p>
          </div>
        </div>
        <div>
          <button class="btn btn-primary shadow-sm hover:scale-105 transition transform">+ New Project</button>
        </div>
      </div>

      <!-- Metrics -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div v-for="(metric, i) in metrics" :key="i"
             class="rounded-xl p-6 text-white bg-gradient-to-br shadow-lg hover:scale-[1.03] transition transform"
             :class="metric.color">
          <div class="text-4xl mb-2">{{ metric.icon }}</div>
          <p class="text-lg">{{ metric.label }}</p>
          <p class="text-3xl font-bold">{{ metric.value }}</p>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-700 mb-4">Recent Activities</h2>
        <ul class="divide-y divide-gray-200">
          <li v-for="(activity, idx) in activities" :key="idx" class="py-3 text-gray-600 flex items-center gap-2 hover:text-blue-600 transition">
            <span>{{ activity }}</span>
          </li>
        </ul>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Smooth hover transition untuk card dan button */
button.btn {
  background-color: #2563eb;
  color: white;
  padding: 0.6rem 1.2rem;
  border-radius: 0.5rem;
}
button.btn:hover {
  background-color: #1d4ed8;
}
</style>
