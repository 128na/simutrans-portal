import { createRoot } from "react-dom/client";
console.log('app1.tsx loaded');

const app = document.getElementById('app-example-1');
if (app) {
  createRoot(app).render(<div>Example app1 loaded</div>);
}
