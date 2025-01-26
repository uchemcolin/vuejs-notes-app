/*import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// Load environment variables based on the mode (development or production)
const apiBaseUrl = process.env.VITE_API_BASE_URL;

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
  ],
  server: {
    proxy: {
      "/api": {
        //target: "http://127.0.0.1:8000/api",
        //target: "https://simple-notes-app-backend.uchemcolin.xyz/api",
        target: apiBaseUrl,
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, ""),
      }
    }
  },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
})*/

import { fileURLToPath, URL } from "node:url";
import { defineConfig, loadEnv } from "vite"; // Import loadEnv to load environment variables
import vue from "@vitejs/plugin-vue";
import vueDevTools from "vite-plugin-vue-devtools";

export default defineConfig(({ mode }) => {
  // Load environment variables based on the current mode
  const env = loadEnv(mode, process.cwd());
  const apiBaseUrl = env.VITE_API_BASE_URL; // Fetch VITE_API_BASE_URL from .env file

  return {
    plugins: [
      vue(),
      vueDevTools(),
    ],
    server: {
      proxy: {
        "/api": {
          target: apiBaseUrl, // Use the loaded environment variable
          changeOrigin: true,
          rewrite: (path) => path.replace(/^\/api/, ""),
        },
      },
    },
    resolve: {
      alias: {
        "@": fileURLToPath(new URL("./src", import.meta.url)),
      },
    },
  };
});

