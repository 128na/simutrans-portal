import Vue from 'vue';
import VueRouter from 'vue-router';

/**
* import components
*/
import PageLogin from '../Components/Pages/PageLogin';
import PageLogout from '../Components/Pages/PageLogout';
import PageRegister from '../Components/Pages/PageRegister';
import PageReset from '../Components/Pages/PageReset';
import PageIndex from '../Components/Pages/PageIndex';
import PageCreateArticle from '../Components/Pages/PageCreateArticle';
import PageEditArticle from '../Components/Pages/PageEditArticle';
import PageEditProfile from '../Components/Pages/PageEditProfile';
import PageAnalyticsArticle from '../Components/Pages/PageAnalyticsArticle';
import store from '../store';
const routes = [
  { name: "login", path: '/login', component: PageLogin },
  { name: "logout", path: '/logout', component: PageLogout },
  { name: "register", path: '/register', component: PageRegister },
  { name: "reset", path: '/reset', component: PageReset },
  { name: "index", path: '/', component: PageIndex },
  { name: "createArticle", path: '/create/:post_type', component: PageCreateArticle },
  { name: "editArticle", path: '/edit/:id', component: PageEditArticle },
  { name: "editProfile", path: '/profile', component: PageEditProfile },
  { name: "analyticsArticle", path: '/analytics', component: PageAnalyticsArticle },
  { path: '*', redirect: { name: 'login' } },
];

const scrollBehavior = (to, from, savedPosition) => {
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      resolve(savedPosition
        ? savedPosition
        : { x: 0, y: 0 }
      )
    }, 100) // transitionの時間と合わせる
  })
};

Vue.use(VueRouter);
const router = new VueRouter({
  base: '/mypage',
  // mode: 'history',
  scrollBehavior,
  routes,
});

router.beforeEach((to, from, next) => {
  store.dispatch('setApiStatusInit');
  next();
});

import route_shortcut from '../mixins/route';
Vue.mixin(route_shortcut);


export default router;

