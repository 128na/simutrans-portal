import axios from "axios";

axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
// CSRF cookie を取得（エラーは無視）
axios.get("/sanctum/csrf-cookie").catch(() => {
  // エラーは無視（ページロード時の初期化処理のため）
});
