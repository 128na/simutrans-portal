import "@testing-library/jest-dom/vitest";

// テスト環境用の環境変数設定
Object.defineProperty(import.meta, "env", {
  value: {
    VITE_API_URL: "http://localhost",
    VITE_APP_URL: "http://localhost",
  },
  writable: false,
  configurable: true,
});
