import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [laravel({
    input: [
        'resources/js/main.js',
        'resources/views/**'
    ],
    refresh: true,
    hotFile: 'cache/vite/vite.hot',
  })],
  build: {
    outDir: './public/assets',
    manifest: true,
    rollupOptions: {
      input: './public/index.html',
    },
  },
    server: {
        origin: 'http://localhost',
    },
});