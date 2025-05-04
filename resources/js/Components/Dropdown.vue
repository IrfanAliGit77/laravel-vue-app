<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  align: { type: String, default: 'right' },
  width: { type: String, default: '48' },
  contentClasses: { type: String, default: 'py-1 bg-white' },
})

const open = ref(false)

function onEscape(e) {
  if (e.key === 'Escape') open.value = false
}

onMounted(() => window.addEventListener('keydown', onEscape))
onUnmounted(() => window.removeEventListener('keydown', onEscape))

const widthClass = computed(() => ({
  '48': 'w-48',
  '56': 'w-56',
}[props.width]))

const alignClass = computed(() => {
  if (props.align === 'left') return 'origin-top-left left-0'
  if (props.align === 'right') return 'origin-top-right right-0'
  return 'origin-top'
})
</script>

<template>
  <div class="relative">
    <div @click="open = !open"><slot name="trigger" /></div>

    <!-- Overlay -->
    <div
      v-show="open"
      class="fixed inset-0 z-40"
      @click="open = false"
    ></div>

    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-show="open"
        class="absolute z-50 mt-2 rounded-md shadow-lg ring-1 ring-black ring-opacity-5"
        :class="[widthClass, alignClass]"
        style="display: none"
      >
        <div :class="contentClasses">
          <slot name="content" />
        </div>
      </div>
    </transition>
  </div>
</template>
