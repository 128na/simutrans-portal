import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
  plugins: [
    // react(),
    tailwindcss(),
    laravel({
      input: [
        "resources/css/front.css",
        "resources/js/front.ts",
        "resources/css/mypage.css",
        "resources/js/mypage.ts",
      ],
      refresh: true,
    }),
  ],
  esbuild: {
    jsx: "automatic",
  },
});
