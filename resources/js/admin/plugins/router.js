import Vue from 'vue';
import VueRouter from 'vue-router';

/**
* import components
*/
import PageIndex from '../Components/Pages/PageIndex';
import PagePhpinfo from '../Components/Pages/PagePhpinfo';
import PageUsers from '../Components/Pages/PageUsers';
import PageCreateUser from '../Components/Pages/PageCreateUser';
import PageArticles from '../Components/Pages/PageArticles';

const routes = [
  { name: "index", path: '/', component: PageIndex },
  { name: "phpinfo", path: '/phpinfo', component: PagePhpinfo },
  { name: "users", path: '/users', component: PageUsers },
  { name: "createUser", path: '/users/create', component: PageCreateUser },
  { name: "articles", path: '/articles', component: PageArticles },
];

Vue.use(VueRouter);
const router = new VueRouter({
  base: '/admin',
  // mode: 'history',
  routes,
});

import { ga_page_view } from "../../mypage/plugins/ga";
router.afterEach((to, from) => {
  ga_page_view(to, '/admin');
});

export default router;

