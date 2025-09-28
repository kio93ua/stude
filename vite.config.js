import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import path from 'node:path'

export default defineConfig({
  plugins: [
    vue(),
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
  resolve: {
    // важливо: без alias на 'vue/dist/*', щоб не було дубля Vue
    dedupe: ['vue'],
    alias: { '@': path.resolve(__dirname, 'resources/js') },
  },
  build: {
    // простий, передбачуваний білд; сорсмапи в проді не потрібні
    sourcemap: false,
    rollupOptions: {
      output: {
        
         manualChunks: { vendor: ['vue'] },
      },
    },
  },
})
