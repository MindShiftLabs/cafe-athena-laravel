import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/login.css',
                'resources/css/register.css',
                'resources/css/admin.css',
                'resources/css/barista.css',
                'resources/css/customer.css',
                'resources/css/global.css',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
