<script setup>
import { Head, Link, router } from '@inertiajs/vue3'

// Terima props yang disediakan oleh HandleInertiaRequests/share()
defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  laravelVersion: {
    type: String,
    required: true,
  },
  phpVersion: {
    type: String,
    required: true,
  },
  // auth.user otomatis disediakan via shared props
})

// Handler untuk logout via Inertia router
function logout() {
  router.post(route('logout'))
}
</script>

<template>
  <Head title="Welcome" />

  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex flex-col items-center justify-center px-6 py-12">
    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-8">
      Welcome to My Application
    </h1>

    <div class="space-x-4">
      <!-- Belum login -->
      <template v-if="canLogin && !$page.props.auth.user">
        <Link
          :href="route('login')"
          class="px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium"
        >
          Log in
        </Link>

        <Link
          v-if="canRegister"
          :href="route('register')"
          class="px-5 py-3 bg-green-600 hover:bg-green-700 text-white rounded-md font-medium"
        >
          Register
        </Link>
      </template>

      <!-- Sudah login -->
      <template v-else-if="$page.props.auth.user">
        <Link
          :href="route('dashboard')"
          class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-medium"
        >
          Dashboard
        </Link>

        <button
          @click="logout"
          class="px-5 py-3 bg-red-600 hover:bg-red-700 text-white rounded-md font-medium"
        >
          Logout
        </button>
      </template>
    </div>

    <footer class="mt-12 text-sm text-gray-500 dark:text-gray-400">
      Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
    </footer>
  </div>
</template>

<style scoped>
/* Tambahkan styling khusus jika diperlukan */
</style>
