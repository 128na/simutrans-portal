import Vue from 'vue';
import VueRouter from 'vue-router';

/**
* import components
*/
import PageShow from '../Components/Pages/PageShow';
import PageList from '../Components/Pages/PageList';
import PageError from '../Components/Pages/PageError';

// import { gaPageView } from './ga';

// import routeShortcut from '../mixins/route';

const routes = [
  { name: 'show', path: '/articles/:slug', component: PageShow },
  { name: 'categoryPak', path: '/category/pak/:size/:slug', component: PageList },
  { name: 'category', path: '/category/:type/:slug', component: PageList },
  { name: 'tag', path: '/tag/:id', component: PageList },
  { name: 'user', path: '/user/:id', component: PageList },
  { name: 'announces', path: '/announces', component: PageList },
  { name: 'pages', path: '/pages', component: PageList },
  { name: 'ranking', path: '/ranking', component: PageList },
  // { name: 'top', path: '/', component: PageTop },
  { name: 'tags', path: '/tags', component: PageList },
  { name: 'advancedSearch', path: '/advancedSearch', component: PageList },
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
