import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        tailwindcss(),
    ],
    build: {
        outDir: 'resources/dist',
        emptyOutDir: true,
        rollupOptions: {
            input: 'resources/css/filament-spatie-lighthouse.css',
            output: {
                assetFileNames: 'filament-spatie-lighthouse.css',
            },
        },
    },
});
