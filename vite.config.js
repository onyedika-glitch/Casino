import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import i18n from 'laravel-vue-i18n/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                base: null,
                includeAbsolute: false,
            }
        }),
        i18n(),
    ],
    build: {
        chunkSizeWarningLimit: 2024, // Ajusta o limite de aviso de tamanho do chunk para 1024KB (1MB)
    }
});
