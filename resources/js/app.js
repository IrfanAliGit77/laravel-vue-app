import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js'; // setelah install ziggy-vue via npm/yarn

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
// resources/js/app.js
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

createInertiaApp({
  title: title => `${title} - ${appName}`,
  resolve: async (name) => {
    const pages = import.meta.glob('./Pages/**/*.vue');

    // Coba muat file .vue secara langsung
    let page = pages[`./Pages/${name}.vue`];

    // Jika tidak ditemukan, coba muat index.vue dari folder
    if (!page) {
      page = pages[`./Pages/${name}/index.vue`];
    }

    // Jika masih tidak ditemukan, lemparkan error
    if (!page) {
      throw new Error(`Halaman tidak ditemukan: ${name}`);
    }

    return await page();
  },  
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue, Ziggy)
      .mount(el);
  },
  progress: { color: '#4B5563' },
});
