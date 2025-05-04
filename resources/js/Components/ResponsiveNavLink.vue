<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  href: { type: String, required: true },
  active: { type: Boolean, default: false },
})

const classes = computed(() =>
  props.active
    ? 'block px-4 py-2 text-indigo-700 bg-indigo-100 font-semibold'
    : 'block px-4 py-2 text-gray-600 hover:bg-gray-50'
)
</script>

<template>
  <Link :href="href" :class="classes">
    <slot />
  </Link>
</template>
