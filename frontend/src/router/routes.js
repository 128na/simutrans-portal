import Error from 'src/pages/Error.vue';

const routes = [
  {
    path: '/mypage',
    component: () => import(/* webpackChunkName: "mypage" */'layouts/MypageLayout.vue'),
    children: [
      {
        name: 'mypage', path: '', meta: { requiresVerified: true }, component: () => import(/* webpackChunkName: "mypage" */'pages/Mypage/Top.vue'),
      },
      {
        name: 'edit', path: 'edit/:id', meta: { requiresVerified: true }, component: () => import(/* webpackChunkName: "mypage" */'pages/Mypage/Edit.vue')
        ,
      },
      {
        name: 'create', path: 'create/:post_type', meta: { requiresVerified: true }, component: () => import(/* webpackChunkName: "mypage" */'pages/Mypage/Create.vue')
        ,
      },
      {
        name: 'analytics', path: 'analytics', meta: { requiresVerified: true }, component: () => import(/* webpackChunkName: "mypage" */'pages/Mypage/Analytics.vue')
        ,
      },
      {
        name: 'profile', path: 'profile', meta: { requiresVerified: true }, component: () => import(/* webpackChunkName: "mypage" */'pages/Mypage/Profile.vue')
        ,
      },
      {
        name: 'redirect', path: 'redirect', meta: { requiresVerified: true }, component: () => import(/* webpackChunkName: "mypage" */'pages/Mypage/Redirect.vue')
        ,
      },
    ],
  },
  {
    path: '/admin',
    component: () => import(/* webpackChunkName: "admin" */ 'layouts/AdminLayout.vue'),
    children: [
      {
        name: 'admin', path: '', meta: { requiresAdmin: true }, component: () => import(/* webpackChunkName: "admin" */ 'src/pages/Admin/Top.vue')
        ,
      },
      {
        name: 'admin.controllOptions', path: 'controll_options', meta: { requiresAdmin: true }, component: () => import(/* webpackChunkName: "admin" */ 'pages/Admin/ControllOptions.vue')
        ,
      },
      {
        name: 'admin.token', path: 'token', meta: { requiresAdmin: true }, component: () => import(/* webpackChunkName: "admin" */ 'pages/Admin/Token.vue')
        ,
      },
      {
        name: 'admin.articles', path: 'articles', meta: { requiresAdmin: true }, component: () => import(/* webpackChunkName: "admin" */ 'pages/Admin/Articles.vue')
        ,
      },
      {
        name: 'admin.users', path: 'users', meta: { requiresAdmin: true }, component: () => import(/* webpackChunkName: "admin" */ 'pages/Admin/Users.vue')
        ,
      },
    ],
  },
  {
    name: 'error',
    path: '/error/:status',
    component: Error,
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    redirect: { name: 'mypage' },
  },
];

export default routes;
