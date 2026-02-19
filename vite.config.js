import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

// Detect DDEV environment — does NOT affect Herd/Laragon/Valet
const isDdev = !!process.env.DDEV_HOSTNAME;
const ddevHost = process.env.DDEV_HOSTNAME || 'localhost';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],

    // Server settings are ONLY applied inside DDEV.
    // Outside DDEV (Herd, Laragon, bare npm) the fallback block
    // is used so Vite uses its own sensible defaults without interference.
    server: isDdev
        ? {
              host: '0.0.0.0',     // listen on all interfaces inside the container
              port: 5173,
              strictPort: true,
              watch: {
                  usePolling: true, // required for file-watching inside Docker
                  interval: 250,
                  ignored: ['**/storage/framework/views/**'],
              },
              hmr: {
                  host: ddevHost,  // e.g. laravel12-boilerplate.ddev.site
                  protocol: 'wss',
                  clientPort: 5173,
              },
          }
        : {
              watch: {
                  ignored: ['**/storage/framework/views/**'],
              },
          },
});
