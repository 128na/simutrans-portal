import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";
import { exec, execSync } from "child_process";
import * as fs from "fs";
import react, { reactCompilerPreset } from "@vitejs/plugin-react";
import babel from "@rolldown/plugin-babel";

export default defineConfig({
  plugins: [
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
    // Fast Refresh・React CompilerはテストではJSXの解釈にのみ必要でメモ化最適化に
    // 意味がなく、babelトランスフォームがテスト実行時間を大幅に増やす(実測で
    // `vitest run`のtransform時間が約5倍・全体で3倍超に悪化)ため、Vitest実行時は
    // 適用しない。JSX自体はViteのデフォルト(esbuild)変換で問題なく処理される。
    ...(process.env.VITEST
      ? []
      : [react(), babel({ presets: [reactCompilerPreset()] })]),
  ],
  build: {
    rollupOptions: {
      output: {},
    },
  },
  test: {
    globals: true,
    environment: "jsdom",
    setupFiles: "./resources/js/__tests__/setup.ts",
    exclude: [".claude/**", "**/node_modules/**", "**/dist/**"],
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
      if (fs.existsSync("resources/js/utils/ja.json")) {
        return;
      }
      try {
        const result = execSync("php artisan lang:export-json");
        if (result) console.log(result.toString());
      } catch (err: unknown) {
        console.warn(
          "Warning: Language export failed. Ensure 'composer install' is run before 'npm run build'.",
          err instanceof Error ? err.message : err
        );
      }
    },
    handleHotUpdate({ file }: { file: string }) {
      if (file.includes("resources/lang/")) {
        exec("php artisan lang:export-json", (err, stdout) => {
          if (stdout) console.log(stdout);
          if (err) {
            console.warn("Warning: Language export failed.", err.message);
          }
        });
      }
    },
  };
}
