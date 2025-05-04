<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'

const showingNav = ref(false)
const { props } = usePage()
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-white shadow-md">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
        <!-- Logo + Links -->
        <div class="flex items-center space-x-4">
          <Link :href="route('dashboard')">
            <ApplicationLogo class="h-10 w-10 text-indigo-600 hover:text-indigo-800" />
          </Link>
          <div class="hidden sm:flex space-x-2">
            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
              Dashboard
            </NavLink>
            <NavLink
              :href="route('role.management')"
              :active="route().current('role.management')"
            >
              Role Management
            </NavLink>
          </div>
        </div>

        <!-- User Dropdown -->
        <div class="hidden sm:flex items-center space-x-4">
          <Dropdown align="right" width="56">
            <template #trigger>
              <button class="flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-50 hover:bg-indigo-100">
                <span class="font-medium text-gray-700">{{ props.auth.user.name }}</span>
                <svg class="h-4 w-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 
                       111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 
                       010-1.414z"
                    clip-rule="evenodd" />
                </svg>
              </button>
            </template>
            <template #content>
              <div class="py-1">
                <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                <DropdownLink :href="route('logout')" method="post" as="button">
                  Log Out
                </DropdownLink>
              </div>
            </template>
          </Dropdown>
        </div>

        <!-- Hamburger -->
        <button
          @click="showingNav = !showingNav"
          class="sm:hidden p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-400"
        >
          <svg
            v-if="!showingNav"
            class="h-6 w-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg
            v-else
            class="h-6 w-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Mobile Menu -->
      <div v-show="showingNav" class="sm:hidden bg-white border-t border-gray-200">
        <ResponsiveNavLink
          :href="route('dashboard')"
          :active="route().current('dashboard')"
        >
          Dashboard
        </ResponsiveNavLink>
        <ResponsiveNavLink
          :href="route('role.management')"
          :active="route().current('role.management')"
        >
          Role Management
        </ResponsiveNavLink>

        <div class="border-t border-gray-200 pt-2 pb-4">
          <ResponsiveNavLink :href="route('profile.edit')">
            Profile
          </ResponsiveNavLink>
          <ResponsiveNavLink :href="route('logout')" method="post" as="button">
            Log Out
          </ResponsiveNavLink>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main class="flex-1">
      <slot />
    </main>
  </div>
</template>
