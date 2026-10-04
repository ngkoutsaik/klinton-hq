import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js', 'resources/css/resume.css'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        assetsInlineLimit: (filePath) => (filePath.endsWith('.woff2') ? true : undefined),
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
