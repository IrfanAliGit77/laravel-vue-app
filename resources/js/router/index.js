// resources/js/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import RoleManagement from '@/Pages/RoleManagement/index.vue';

const routes = [
  {
    path: '/role-management',
    name: 'RoleManagement',
    component: RoleManagement,
  },
  // Rute lainnya...
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
