import { useAuthStore } from 'src/store/auth';
import {
  createRouter, createMemoryHistory, createWebHistory, createWebHashHistory,
} from 'vue-router';
import routes from './routes';

/*
 * If not building with SSR mode, you can
 * directly export the Router instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Router instance.
 */

const createHistory = process.env.SERVER
  ? createMemoryHistory
  : (process.env.VUE_ROUTER_MODE === 'history' ? createWebHistory : createWebHashHistory);

const Router = createRouter({
  scrollBehavior: () => ({ left: 0, top: 0 }),
  routes,

  // Leave this as is and make changes in quasar.conf.js instead!
  // quasar.conf.js -> build -> vueRouterMode
  // quasar.conf.js -> build -> publicPath
  history: createHistory(process.env.MODE === 'ssr' ? void 0 : process.env.VUE_ROUTER_BASE),
});

Router.beforeEach((to, from, next) => {
  const store = useAuthStore();
  if (to.matched.some((record) => record.meta.requiresAuth)) {
    if (!store.isLoggedIn) {
      return next({ replace: true, name: 'login' });
    }
  }
  if (to.matched.some((record) => record.meta.requiresVerified)) {
    if (!store.isLoggedIn) {
      return next({ replace: true, name: 'login' });
    }
    if (!store.isVerified) {
      return next({ replace: true, name: 'error', params: { status: 401 } });
    }
  }
  if (to.matched.some((record) => record.meta.requiresAdmin)) {
    if (!store.isLoggedIn) {
      return next({ replace: true, name: 'login' });
    }
    if (!store.isAdmin) {
      return next({ replace: true, name: 'error', params: { status: 401 } });
    }
  }
  return next();
});

export default Router;
