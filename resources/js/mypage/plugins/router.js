import Vue from 'vue';
import VueRouter from 'vue-router';

/**
* import components
*/
import PageLogin from '../Components/Pages/PageLogin';
import PageLogout from '../Components/Pages/PageLogout';
import PageReset from '../Components/Pages/PageReset';
import PageIndex from '../Components/Pages/PageIndex';
import PageCreateArticle from '../Components/Pages/PageCreateArticle';
import PageEditArticle from '../Components/Pages/PageEditArticle';
import PageEditProfile from '../Components/Pages/PageEditProfile';
import PageAnalyticsArticle from '../Components/Pages/PageAnalyticsArticle';
import PageInvitation from '../Components/Pages/PageInvitation';
import store from '../store';

import routeShortcut from '../mixins/route';
import gtm from '../../gtm';

const routes = [
  { name: 'login', path: '/login', component: PageLogin },
  { name: 'logout', path: '/logout', component: PageLogout },
  { name: 'reset', path: '/reset', component: PageReset },
  { name: 'index', path: '/', component: PageIndex },
  { name: 'createArticle', path: '/create/:post_type', component: PageCreateArticle },
  { name: 'editArticle', path: '/edit/:id', component: PageEditArticle },
  { name: 'editProfile', path: '/profile', component: PageEditProfile },
  { name: 'analyticsArticle', path: '/analytics', component: PageAnalyticsArticle },
  { name: 'invitation', path: '/invitation', component: PageInvitation },
  { path: '*', redirect: { name: 'login' } }
];

const scrollBehavior = (to, from, savedPosition) => {
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      resolve(savedPosition || { x: 0, y: 0 }
      );
    }, 100); // transitionの時間と合わせる
  });
};

Vue.use(VueRouter);
const router = new VueRouter({
  base: '/mypage',
  // mode: 'history',
  scrollBehavior,
  routes
});

router.beforeEach((to, from, next) => {
  store.dispatch('setApiStatusInit');
  next();
});
router.afterEach((to, from) => {
  gtm.resetDataLayer(process.env.MIX_GTM);
});
Vue.mixin(routeShortcut);

export default router;
