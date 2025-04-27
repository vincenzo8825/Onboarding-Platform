import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/style.css',
                'resources/js/app.js',
                'resources/css/pages/welcome.css',
                'resources/css/pages/auth.css',
                'resources/css/pages/admin.css',
                'resources/css/pages/employee.css',
                'resources/css/pages/error.css',
                'resources/css/pages/static-pages.css',
                'resources/css/pages/notifications.css',
            ],
            refresh: true,
        }),
    ],
});
