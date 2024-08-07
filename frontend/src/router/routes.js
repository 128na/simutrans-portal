import Layout from 'layouts/FrontLayout.vue';
import Top from 'pages/Front/Top.vue';
import Show from 'pages/Front/Show.vue';
import List from 'pages/Front/List.vue';
import Tags from 'pages/Front/Tags.vue';
import DiscordInvite from 'pages/Front/DiscordInvite.vue';
import Social from 'pages/Front/Social.vue';
import Error from 'src/pages/Error.vue';

const routes = [
  {
    path: '/',
    component: Layout,
    children: [
      { name: 'top', path: '', component: Top },
      { name: 'user', path: 'users/:idOrNickname', component: List },
      { name: 'show', path: 'users/:idOrNickname/:slug', component: Show },
      { name: 'categoryPak', path: 'categories/pak/:size/:slug', component: List },
      { name: 'category', path: 'categories/:type/:slug', component: List },
      { name: 'tags', path: 'tags', component: Tags },
      { name: 'tag', path: 'tags/:id', component: List },
      { name: 'announces', path: 'announces', component: List },
      { name: 'pages', path: 'pages', component: List },
      { name: 'ranking', path: 'ranking', component: List },
      { name: 'search', path: 'search', component: List },
      { name: 'discordInvite', path: 'invite-simutrans-interact-meeting', component: DiscordInvite },
      { name: 'social', path: 'social', component: Social },
    ],
  },
  {
    path: '/mypage',
    component: () => import(/* webpackChunkName: "mypage" */'layouts/MypageLayout.vue'),
    children: [
      {
        name: 'reset', path: 'reset/:token', meta: { requiresGuest: true }, component: () => import(/* webpackChunkName: "mypage" */'pages/Mypage/Reset.vue'),
      },
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
        name: 'security', path: 'security', meta: { requiresVerified: true }, component: () => import(/* webpackChunkName: "mypage" */'pages/Mypage/Security.vue')
        ,
      },
      {
        name: 'invitation', path: 'invitation', meta: { requiresVerified: true }, component: () => import(/* webpackChunkName: "mypage" */'pages/Mypage/Invitation.vue')
        ,
      },
      {
        name: 'redirect', path: 'redirect', meta: { requiresVerified: true }, component: () => import(/* webpackChunkName: "mypage" */'pages/Mypage/Redirect.vue')
        ,
      },
      {
        name: 'invite', path: 'invite/:code', meta: { requiresGuest: true }, component: () => import(/* webpackChunkName: "mypage" */'pages/Mypage/Invite.vue')
        ,
      },
      {
        name: 'requiresVerified', path: 'requires-verified', meta: { requiresAuth: true }, component: () => import(/* webpackChunkName: "mypage" */'pages/Mypage/RequiresVerified.vue')
        ,
      },
      {
        name: 'login', path: 'login', meta: { requiresGuest: true }, component: () => import(/* webpackChunkName: "mypage" */'pages/Mypage/Login.vue')
        ,
      },
      {
        name: 'logout', path: 'logout', meta: { requiresAuth: true }, component: () => import(/* webpackChunkName: "mypage" */'pages/Mypage/Logout.vue')
        ,
      },
      { name: 'forget', path: 'forget', component: () => import(/* webpackChunkName: "mypage" */'pages/Mypage/Forget.vue') },
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
    component: Error,
  },
];

export default routes;
