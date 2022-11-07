import FrontLayout from 'layouts/FrontLayout.vue';
import FrontTop from 'pages/Front/Top.vue';
import FrontShow from 'pages/Front/Show.vue';
import FrontList from 'pages/Front/List.vue';
import FrontTags from 'pages/Front/Tags.vue';
import MypageLayout from 'layouts/MypageLayout.vue';
import MypageReset from 'pages/Mypage/Reset.vue';
import MypageTop from 'pages/Mypage/Top.vue';
import MypageEdit from 'pages/Mypage/Edit.vue';
import MypageCreate from 'pages/Mypage/Create.vue';
import MypageAnalytics from 'pages/Mypage/Analytics.vue';
import MypageProfile from 'pages/Mypage/Profile.vue';
import MypageInvitation from 'pages/Mypage/Invitation.vue';
import MypageInvite from 'pages/Mypage/Invite.vue';
import MypageRequiresVerified from 'pages/Mypage/RequiresVerified.vue';
import MypageVerify from 'pages/Mypage/Verify.vue';
import MypageLogin from 'pages/Mypage/Login.vue';
import MypageLogout from 'pages/Mypage/Logout.vue';
import MypageForget from 'pages/Mypage/Forget.vue';
import AdminLayout from 'layouts/AdminLayout.vue';
import AdminIndex from 'pages/Index.vue';
import AdminToken from 'pages/Admin/Token.vue';
import AdminArticles from 'pages/Admin/Articles.vue';
import AdminUsers from 'pages/Admin/Users.vue';
import Error from 'src/pages/Error.vue';

const routes = [
  {
    path: '/',
    component: FrontLayout,
    children: [
      { name: 'top', path: '', component: FrontTop },
      { name: 'show', path: 'articles/:slug', component: FrontShow },
      { name: 'categoryPak', path: 'category/pak/:size/:slug', component: FrontList },
      { name: 'category', path: 'category/:type/:slug', component: FrontList },
      { name: 'tag', path: 'tag/:id', component: FrontList },
      { name: 'user', path: 'user/:id', component: FrontList },
      { name: 'announces', path: 'announces', component: FrontList },
      { name: 'pages', path: 'pages', component: FrontList },
      { name: 'ranking', path: 'ranking', component: FrontList },
      { name: 'search', path: 'search', component: FrontList },
      { name: 'tags', path: 'tags', component: FrontTags },
    ],
  },
  {
    path: '/mypage',
    component: MypageLayout,
    children: [
      {
        name: 'reset', path: 'reset/:token', meta: { requiresGuest: true }, component: MypageReset,
      },
      {
        name: 'mypage', path: '', meta: { requiresVerified: true }, component: MypageTop,
      },
      {
        name: 'edit', path: 'edit/:id', meta: { requiresVerified: true }, component: MypageEdit,
      },
      {
        name: 'create', path: 'create/:post_type', meta: { requiresVerified: true }, component: MypageCreate,
      },
      {
        name: 'analytics', path: 'analytics', meta: { requiresVerified: true }, component: MypageAnalytics,
      },
      {
        name: 'profile', path: 'profile', meta: { requiresVerified: true }, component: MypageProfile,
      },
      {
        name: 'invitation', path: 'invitation', meta: { requiresVerified: true }, component: MypageInvitation,
      },
      {
        name: 'invite', path: 'invite/:code', meta: { requiresGuest: true }, component: MypageInvite,
      },
      {
        name: 'requiresVerified', path: 'requires-verified', meta: { requiresAuth: true }, component: MypageRequiresVerified,
      },
      {
        name: 'verify', path: 'verify/:userId/:hash', meta: { requiresAuth: true }, component: MypageVerify,
      },
      {
        name: 'login', path: 'login', meta: { requiresGuest: true }, component: MypageLogin,
      },
      {
        name: 'logout', path: 'logout', meta: { requiresAuth: true }, component: MypageLogout,
      },
      { name: 'forget', path: 'forget', component: MypageForget },
    ],
  },
  {
    path: '/admin',
    component: AdminLayout,
    children: [
      {
        name: 'admin', path: '', meta: { requiresAdmin: true }, component: AdminIndex,
      },
      {
        name: 'admin.token', path: 'token', meta: { requiresAdmin: true }, component: AdminToken,
      },
      {
        name: 'admin.articles', path: 'articles', meta: { requiresAdmin: true }, component: AdminArticles,
      },
      {
        name: 'admin.users', path: 'users', meta: { requiresAdmin: true }, component: AdminUsers,
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
