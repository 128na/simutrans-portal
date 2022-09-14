import Vue from 'vue';
import VueRouter from 'vue-router';

/**
* import components
*/
import PageShow from '../Components/Pages/PageShow';
import PageError from '../Components/Pages/PageError';

// import { gaPageView } from './ga';

// import routeShortcut from '../mixins/route';

const routes = [
  { name: 'show', path: '/articles/:slug', component: PageShow },
  { path: '*', redirect: { name: 'notFound' } },
  { name: 'error', path: '/error/:status', component: PageError }
];

// const scrollBehavior = (to, from, savedPosition) => {
//   return new Promise((resolve, reject) => {
//     setTimeout(() => {
//       resolve(savedPosition || { x: 0, y: 0 }
//       );
//     }, 100); // transitionの時間と合わせる
//   });
// };

Vue.use(VueRouter);
const router = new VueRouter({
  base: '/',
  mode: 'history',
  // scrollBehavior,
  routes
});

router.afterEach((to, from) => {
  // gaPageView(to);
});
// Vue.mixin(routeShortcut);

export default router;
