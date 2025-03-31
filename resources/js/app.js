import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#FF9800',
    },
}).catch(error => {
    console.error('Inertia app initialization error:', error);
    document.body.innerHTML = `<div style="padding: 2rem; text-align: center;">
        <h1 style="color: red;">Application Error</h1>
        <p>There was an error initializing the application. Please check the console for more details.</p>
        <pre style="text-align: left; background: #f5f5f5; padding: 1rem; border-radius: 0.5rem; overflow: auto;">${error}</pre>
    </div>`;
});
