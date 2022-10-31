import axios from 'axios';

const token = document.head.querySelector('meta[name="csrf-token"]');
if (!token) {
  // eslint-disable-next-line no-console
  console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] = token?.content || 'dummy';

export const useFrontApi = () => ({
  get(url) {
    return axios.get(url);
  },
  // conversion
  postConversion(slug) {
    return axios.post(`/api/v3/conversion/${slug}`);
  },
  postShown(slug) {
    return axios.post(`/api/v3/shown/${slug}`);
  },
  // top
  fetchSidebar() {
    return axios.get('/api/v3/front/sidebar');
  },
  // list
  fetchCategoryPak(size, slug, page = 1) {
    return axios.get(`/api/v3/front/category/pak/${size}/${slug}`, { params: { page } });
  },
  fetchCategory(type, slug, page = 1) {
    return axios.get(`/api/v3/front/category/${type}/${slug}`, { params: { page } });
  },
  fetchTag(id, page = 1) {
    return axios.get(`/api/v3/front/tag/${id}`, { params: { page } });
  },
  fetchUser(id, page = 1) {
    return axios.get(`/api/v3/front/user/${id}`, { params: { page } });
  },
  fetchAnnounces(page = 1) {
    return axios.get('/api/v3/front/announces', { params: { page } });
  },
  fetchPages(page = 1) {
    return axios.get('/api/v3/front/pages', { params: { page } });
  },
  fetchRanking(page = 1) {
    return axios.get('/api/v3/front/ranking', { params: { page } });
  },
  fetchSearch(word, page = 1) {
    return axios.get('/api/v3/front/search', { params: { word, page } });
  },
  // show
  fetchArticle(slug) {
    return axios.get(`/api/v3/front/articles/${slug}`);
  },
  fetchTags() {
    return axios.get('/api/v3/front/tags');
  },
});

export const useMypageApi = () => ({
  // auth
  postLogin(params) {
    return axios.post('/api/v2/login', params);
  },
  postLogout() {
    return axios.post('/api/v2/logout');
  },
  resend() {
    return axios.post('/api/v2/email/resend');
  },
  forget(params) {
    return axios.post('/api/v2/password/email', params);
  },
  reset(params) {
    return axios.post('/api/v2/email/reset', params);
  },
  verify(userId, hash, expires, signature) {
    return axios.get(`/api/v2/email/verify/${userId}/${hash}`, {
      params: { expires, signature },
    });
  },
  getToken() {
    return axios.get('/api/v2/mypage/token');
  },

  // user profile
  fetchUser() {
    return axios.get('/api/v2/mypage/user');
  },
  updateUser(user) {
    return axios.post('/api/v2/mypage/user', { user });
  },

  // article
  fetchArticles() {
    return axios.get('/api/v2/mypage/articles');
  },
  createArticle(params) {
    return axios.post('/api/v2/mypage/articles', params);
  },
  updateArticle(params) {
    return axios.post(`/api/v2/mypage/articles/${params.article.id}`, params);
  },
  // article options
  fetchOptions() {
    return axios.get('/api/v2/mypage/options');
  },

  // tag
  fetchTags(name) {
    return axios.get(`/api/v2/mypage/tags?name=${name}`);
  },
  storeTag(name) {
    return axios.post('/api/v2/mypage/tags', { name });
  },

  // attachments
  fetchAttachments() {
    return axios.get('/api/v2/mypage/attachments');
  },
  storeAttachment(form) {
    return axios.post('/api/v2/mypage/attachments', form, {
      headers: { 'content-type': 'multipart/form-data' },
    });
  },
  deleteAttachment(attachmentId) {
    return axios.delete(`/api/v2/mypage/attachments/${attachmentId}`);
  },
  // analytics
  fetchAnalytics(params) {
    return axios.get('/api/v2/mypage/analytics', { params });
  },

  // 一括DL
  fetchUserBulkZip() {
    return axios.get('/api/v3/mypage/bulk-zip');
  },

  // invitation
  fetchInvites() {
    return axios.get('/api/v3/mypage/invitation_code');
  },
  updateInvitationCode() {
    return axios.post('/api/v3/mypage/invitation_code');
  },
  deleteInvitationCode() {
    return axios.delete('/api/v3/mypage/invitation_code');
  },
  invite(code, params) {
    return axios.post(`/api/v3/mypage/invite/${code}`, params);
  },
});

export const useAdminApi = () => ({
  // auth
  fetchArticles() {
    return axios.get('/api/v2/admin/articles');
  },
  putArticle(id, params) {
    return axios.put(`/api/v2/admin/article/${id}`, params);
  },
  deleteArticle(id) {
    return axios.delete(`/api/v2/admin/article/${id}`);
  },
  fetchUsers() {
    return axios.get('/api/v2/admin/users');
  },
  deleteUser(id) {
    return axios.delete(`/api/v2/admin/users/${id}`);
  },
});
