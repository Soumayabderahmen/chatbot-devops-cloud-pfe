import { defineConfig } from 'vitest/config'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [
    vue(),
    {
      name: 'mock-assets',
      resolveId(id) {
        // Handle absolute paths starting with /images/
        if (id.startsWith('/images/')) {
          return id
        }
        // Handle other asset types
        if (/\.(png|jpe?g|gif|svg|webp|ico|css|scss|sass|less)$/i.test(id)) {
          return id
        }
        return null
      },
      load(id) {
        // Handle image assets
        if (/\.(png|jpe?g|gif|svg|webp|ico)$/i.test(id)) {
          return `export default "${id}"`
        }
        // Handle CSS assets
        if (/\.(css|scss|sass|less)$/i.test(id)) {
          return ''
        }
        return null
      }
    }
  ],
  test: {
    globals: true,
    environment: 'jsdom',
    setupFiles: './vitest.setup.js',
    include: ['resources/js/tests/**/*.test.js'],
    transformMode: {
      web: [/\.[jt]sx?$/, /\.vue$/],
      ssr: [/\.[jt]sx?$/]
    },
    coverage: {
      provider: 'v8',
      reportsDirectory: './coverage',
      includeSource: true,
      reporter: ['text', 'json', 'lcov', 'cobertura'],
      exclude: [
        '**/*.test.js',
        '**/tests/**',
        '**/node_modules/**',
        '**/.{git,cache,output,temp}/**',
        '**/vendor/**',
        '**/*.min.js',
        '**/*.min.css',
        '**/bootstrap*',
        '**/*bundle*.js',
        '**/public/**',
        '**/storage/**',
      ],
      include: [
        'resources/js/**/*.js',
        'resources/js/**/*.vue',
        '!resources/js/tests/**'
      ],
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/js'),
      '/images': path.resolve(__dirname, 'public/images'),
    },
  },
  define: {
    __VUE_PROD_DEVTOOLS__: false,
  }
})
