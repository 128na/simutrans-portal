import "@testing-library/jest-dom/vitest";

// テスト環境用の環境変数設定
Object.defineProperty(import.meta, "env", {
  value: {
    VITE_APP_URL: "http://localhost",
  },
  writable: false,
  configurable: true,
});
