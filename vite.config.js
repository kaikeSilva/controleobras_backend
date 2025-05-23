import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'; 
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    logLevel: 'info', 
    plugins: [
        laravel({
            input: ['resources/sass/admin_panel.scss', 'resources/js/admin_panel.js'],
            refresh: false, 
        }),
        vue(), 
        tailwindcss(),
    ],
    server: { 
        host: '0.0.0.0',         
        port: 5173,             
        cors: true,             
        hmr: {
            host: '127.0.0.1',   
        },
        origin: 'http://127.0.0.1:5173', 
        watch: {
            ignored: (path) => {
                if (path.endsWith('/laravel-journal') || path.endsWith('\\laravel-journal') || path === 'laravel-journal') {
                    return true; 
                }
                if (path.endsWith('/laravel') || path.endsWith('\\laravel') || path === 'laravel') {
                    return true; 
                }
                return false;
            }
        },
    },
});
