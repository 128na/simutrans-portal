import axios from 'axios';

const token = document.head.querySelector('meta[name="csrf-token"]');
if (!token) {
  // eslint-disable-next-line no-console
  console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

const api = axios.create({
  // baseURL: process.env.BACKEND_URL,
  headers: {
    common: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': token?.content || 'dummy',
    },
  },
});

export const useFrontApi = () => ({
  get(url) {
    return api.get(url);
  },
  // conversion
  postConversion(slug) {
    return api.post(`/api/v3/conversion/${slug}`);
  },
  postShown(slug) {
    return api.post(`/api/v3/shown/${slug}`);
  },
  // top
  fetchSidebar() {
    return api.get('/api/v3/front/sidebar');
  },
  // list
  fetchCategoryPak(size, slug, page = 1) {
    return api.get(`/api/v3/front/category/pak/${size}/${slug}`, { params: { page } });
  },
  fetchCategory(type, slug, page = 1) {
    return api.get(`/api/v3/front/category/${type}/${slug}`, { params: { page } });
  },
  fetchTag(id, page = 1) {
    return api.get(`/api/v3/front/tag/${id}`, { params: { page } });
  },
  fetchUser(id, page = 1) {
    return api.get(`/api/v3/front/user/${id}`, { params: { page } });
  },
  fetchAnnounces(page = 1) {
    return api.get('/api/v3/front/announces', { params: { page } });
  },
  fetchPages(page = 1) {
    return api.get('/api/v3/front/pages', { params: { page } });
  },
  fetchRanking(page = 1) {
    return api.get('/api/v3/front/ranking', { params: { page } });
  },
  fetchSearch(word, page = 1) {
    return api.get('/api/v3/front/search', { params: { word, page } });
  },
  // show
  fetchArticle(slug) {
    return api.get(`/api/v3/front/articles/${slug}`);
  },
  fetchTags() {
    return api.get('/api/v3/front/tags');
  },
});

export const useMypageApi = () => ({
  // auth
  postLogin(params) {
    return api.post('/api/v2/login', params);
  },
  postLogout() {
    return api.post('/api/v2/logout');
  },
  resend() {
    return api.post('/api/v2/email/resend');
  },
  reset(params) {
    return api.post('/api/v2/password/email', params);
  },
  verify(userId, hash, expires, signature) {
    return api.get(`/api/v2/email/verify/${userId}/${hash}`, {
      params: { expires, signature },
    });
  },

  // user profile
  fetchUser() {
    return api.get('/api/v2/mypage/user');
  },
  updateUser(user) {
    return api.post('/api/v2/mypage/user', { user });
  },

  // article
  fetchArticles() {
    return api.get('/api/v2/mypage/articles');
  },
  createArticle(params) {
    return api.post('/api/v2/mypage/articles', params);
  },
  updateArticle(params) {
    return api.post(`/api/v2/mypage/articles/${params.article.id}`, params);
  },
  // article options
  fetchOptions() {
    return api.get('/api/v2/mypage/options');
  },

  // tag
  fetchTags(name) {
    return api.get(`/api/v2/mypage/tags?name=${name}`);
  },
  storeTag(name) {
    return api.post('/api/v2/mypage/tags', { name });
  },

  // attachments
  fetchAttachments() {
    return api.get('/api/v2/mypage/attachments');
  },
  storeAttachment(form) {
    return api.post('/api/v2/mypage/attachments', form, {
      headers: { 'content-type': 'multipart/form-data' },
    });
  },
  deleteAttachment(attachmentId) {
    return api.delete(`/api/v2/mypage/attachments/${attachmentId}`);
  },
  // analytics
  fetchAnalytics(params) {
    return api.get('/api/v2/mypage/analytics', { params });
  },

  // 一括DL
  fetchUserBulkZip() {
    return api.get('/api/v3/mypage/bulk-zip');
  },

  // invitation
  fetchInvites() {
    return api.get('/api/v3/mypage/invitation_code');
  },
  updateInvitationCode() {
    return api.post('/api/v3/mypage/invitation_code');
  },
  deleteInvitationCode() {
    return api.delete('/api/v3/mypage/invitation_code');
  },
});
