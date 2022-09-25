import Vue from 'vue';
import VueRouter from 'vue-router';
/**
* import components
*/
import PageTop from '../Components/Pages/PageTop';
import PageShow from '../Components/Pages/PageShow';
import PageList from '../Components/Pages/PageList';
import PageTags from '../Components/Pages/PageTags';
import PageError from '../Components/Pages/PageError';
import gtm from '../../gtm';

const routes = [
  { name: 'top', path: '/', component: PageTop },
  { name: 'show', path: '/articles/:slug', component: PageShow },
  { name: 'categoryPak', path: '/category/pak/:size/:slug', component: PageList },
  { name: 'category', path: '/category/:type/:slug', component: PageList },
  { name: 'tag', path: '/tag/:id', component: PageList },
  { name: 'user', path: '/user/:id', component: PageList },
  { name: 'announces', path: '/announces', component: PageList },
  { name: 'pages', path: '/pages', component: PageList },
  { name: 'ranking', path: '/ranking', component: PageList },
  { name: 'tags', path: '/tags', component: PageTags },
  { name: 'search', path: '/search', component: PageList },
  { path: '*', redirect: { name: 'notFound' } },
  { name: 'error', path: '/error/:status', component: PageError }
];

const scrollBehavior = (to, from, savedPosition) => {
  return savedPosition || { x: 0, y: 0 };
};

Vue.use(VueRouter);
const router = new VueRouter({
  base: '/',
  mode: 'history',
  scrollBehavior,
  routes
});
router.afterEach((to, from) => {
  gtm.resetDataLayer(process.env.MIX_GTM);
});
export default router;
