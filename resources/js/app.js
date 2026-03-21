import './bootstrap';
import '../css/app.css';
import can from '../js/helpers/can';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

// Blade layouts (e.g. admin) also @vite this file but have no Inertia mount node — skip SPA bootstrap.
const inertiaEl = document.querySelector('[data-page]') ?? document.getElementById('app');

if (inertiaEl) {
    createInertiaApp({
        title: (title) => `${title}`,
        resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
        setup({ el, App, props, plugin }) {
            return createApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue, Ziggy)
                .use(can)
                .mount(el);
        },
        progress: {
            color: '#4B5563',
        },
    });
}

