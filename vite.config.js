import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ mode }) => {
  const isProd = mode === 'production';

  return {
    plugins: [
      laravel({
        input: ['resources/css/app.css', 'resources/js/app.js'],
        buildDirectory: 'build',
        refresh: !isProd,
        integrity: isProd,
      }),
      vue({
        template: { transformAssetUrls: { base: null, includeAbsolute: false } },
      }),
    ],
    resolve: {
      alias: {
        vue: 'vue/dist/vue.esm-bundler.js',
        '@': '/resources/js',
      },
    },
    define: {
      __VUE_OPTIONS_API__: true,
      __VUE_PROD_DEVTOOLS__: false,
    },
    build: {
      target: 'es2018',
      minify: 'esbuild',
      cssCodeSplit: true,
      sourcemap: false,
      assetsInlineLimit: 4096,
      manifest: true, 
      rollupOptions: {
        output: {
          manualChunks: { vue: ['vue'], vendor: ['axios', 'marked'] },
          entryFileNames: 'assets/[name]-[hash].js',
          chunkFileNames: 'assets/[name]-[hash].js',
          assetFileNames: 'assets/[name]-[hash][extname]',
        },
      },
      chunkSizeWarningLimit: 1000,
    },
    esbuild: { drop: isProd ? ['console', 'debugger'] : [] },
  };
});
