import tailwindcss from "@tailwindcss/vite";
import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";
import { exec } from "child_process";

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
      ],
      refresh: true,
    }),
  ],
  esbuild: {
    jsx: "automatic",
  },
});

function LaravelLangWatcher() {
  return {
    name: "laravel-lang-watcher",
    buildStart() {
      exec("php artisan lang:export-json", (err, stdout) => {
        if (stdout) console.log(stdout);
        if (err) console.error(err);
      });
    },
    handleHotUpdate({ file }: { file: string }) {
      if (file.includes("resources/lang/")) {
        exec("php artisan lang:export-json", (err, stdout) => {
          if (stdout) console.log(stdout);
          if (err) console.error(err);
        });
      }
    },
  };
}
