import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0', // This tells Vite to listen on all available network interfaces
        hmr: {
            host: 'localhost', // Hot Module Replacement should still point to localhost for your own browser
        }
    },
});
