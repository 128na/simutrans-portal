import tailwindcss from "@tailwindcss/vite";
import react from "@vitejs/plugin-react";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
  plugins: [
    // react(),
    tailwindcss(),
    laravel({
      input: ["resources/css/app.css", "resources/js/front.ts"],
      refresh: true,
    }),
  ],
  esbuild: {
    jsx: "automatic",
  },
});
