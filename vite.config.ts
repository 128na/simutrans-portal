import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";
import { exec } from "child_process";
import * as fs from "fs";

export default defineConfig({
  plugins: [
    // react(),
    LaravelLangWatcher(),
    tailwindcss(),
    laravel({
      input: [
        "resources/css/front.css",
        "resources/js/front.ts",
        "resources/css/mypage.css",
        "resources/js/mypage.ts",
        "resources/css/admin.css",
        "resources/js/admin.ts",
      ],
      refresh: true,
    }),
  ],
  esbuild: {
    jsx: "automatic",
  },
  build: {
    rollupOptions: {
      output: {
        manualChunks: {
          // React関連を分離
          react: ["react", "react-dom"],
          // 大きなライブラリを分離
          chart: ["recharts"],
          // 記事表示コンポーネントを分離
          "article-components": [
            "./resources/js/features/articles/components/ArticleBase.tsx",
            "./resources/js/features/articles/components/postType/AddonPost.tsx",
            "./resources/js/features/articles/components/postType/AddonIntroduction.tsx",
            "./resources/js/features/articles/components/postType/Markdown.tsx",
            "./resources/js/features/articles/components/postType/Page.tsx",
          ],
          "playground-components": [
            "./resources/js/mypage/pages/PlaygroundPage.tsx",
          ],
        },
      },
    },
  },
  test: {
    globals: true,
    environment: "jsdom",
    setupFiles: "./resources/js/__tests__/setup.ts",
    coverage: {
      provider: "v8",
      reporter: ["text", "json", "html"],
      include: ["resources/js/**/*.{ts,tsx}"],
      exclude: [
        "resources/js/__tests__/**",
        "resources/js/**/*.d.ts",
        "resources/js/vite-env.d.ts",
      ],
    },
  },
});

function LaravelLangWatcher() {
  return {
    name: "laravel-lang-watcher",
    buildStart() {
      // Check if language files already exist to avoid unnecessary PHP execution
      if (fs.existsSync("resources/js/utils/ja.json")) {
        return;
      }
      executeLangExport();
    },
    handleHotUpdate({ file }: { file: string }) {
      if (file.includes("resources/lang/")) {
        executeLangExport();
      }
    },
  };
}

function executeLangExport() {
  exec("php artisan lang:export-json", (err, stdout) => {
    if (stdout) console.log(stdout);
    if (err) {
      console.warn(
        "Warning: Language export failed. Ensure 'composer install' is run before 'npm run build'.",
        err.message
      );
    }
  });
}
