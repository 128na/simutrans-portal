const routes = [
  {
    path: '/',
    component: () => import('layouts/FrontLayout.vue'),
    children: [
      { name: 'top', path: '', component: () => import('pages/Front/Top.vue') },
      { name: 'show', path: '/articles/:slug', component: () => import('pages/Index.vue') },
      { name: 'categoryPak', path: '/category/pak/:size/:slug', component: () => import('pages/Front/List.vue') },
      { name: 'category', path: '/category/:type/:slug', component: () => import('pages/Front/List.vue') },
      { name: 'tag', path: '/tag/:id', component: () => import('pages/Front/List.vue') },
      { name: 'user', path: '/user/:id', component: () => import('pages/Front/List.vue') },
      { name: 'announces', path: '/announces', component: () => import('pages/Front/List.vue') },
      { name: 'pages', path: '/pages', component: () => import('pages/Front/List.vue') },
      { name: 'ranking', path: '/ranking', component: () => import('pages/Front/List.vue') },
      { name: 'search', path: '/search', component: () => import('pages/Front/List.vue') },
      { name: 'tags', path: '/tags', component: () => import('pages/Index.vue') },
    ],
  },
  {
    path: '/mypage',
    component: () => import('layouts/MypageLayout.vue'),
    children: [
      { name: 'mypage', path: '', component: () => import('pages/Index.vue') },
    ],
  },
  {
    path: '/admin',
    component: () => import('layouts/AdminLayout.vue'),
    children: [
      { name: 'admin', path: '', component: () => import('pages/Index.vue') },
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/Error404.vue'),
  },
];

export default routes;
