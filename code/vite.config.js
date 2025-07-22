import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/js/app.js'],
      refresh: false,
    }),
  ],
  build: {
    outDir: 'public/build',
    emptyOutDir: true,
  },
  server: {
    hmr: false,
  },
});
