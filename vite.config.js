import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    logLevel: 'info', // Add more verbose logging
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: false, // Disable Laravel plugin's refresh
        }),
        tailwindcss(),
    ],
    server: { 
        watch: {
            ignored: (path) => {
                // Log the path being tested by the watcher
                // console.log(`[Watcher Test] Path: ${path}`); 
                // Temporarily disable console.log to avoid flooding, enable if needed for deep debug

                if (path.endsWith('/laravel-journal') || path.endsWith('\\laravel-journal') || path === 'laravel-journal') {
                    // console.log(`[Watcher Ignored] Matched laravel-journal: ${path}`);
                    return true; // Ignore laravel-journal
                }
                if (path.endsWith('/laravel') || path.endsWith('\\laravel') || path === 'laravel') {
                    // console.log(`[Watcher Ignored] Matched laravel: ${path}`);
                    return true; // Ignore laravel
                }
                // You can add more conditions here for other files if needed
                // e.g., path.includes('.log') to ignore all log files

                // Default: do not ignore
                return false;
            }
        },
    },
});
