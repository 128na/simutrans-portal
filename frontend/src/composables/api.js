import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

export const useFrontApi = () => ({
  get(url) {
    return axios.get(url);
  },
  // conversion
  postConversion(slug) {
    return axios.post(`/api/conversion/${slug}`);
  },
  postShown(slug) {
    return axios.post(`/api/shown/${slug}`);
  },
  // top
  fetchSidebar() {
    return axios.get('/api/front/sidebar');
  },
  // list
  fetchCategoryPak(size, slug, page = 1) {
    return axios.get(`/api/front/category/pak/${size}/${slug}`, { params: { page } });
  },
  fetchCategory(type, slug, page = 1) {
    return axios.get(`/api/front/category/${type}/${slug}`, { params: { page } });
  },
  fetchTag(id, page = 1) {
    return axios.get(`/api/front/tag/${id}`, { params: { page } });
  },
  fetchUser(id, page = 1) {
    return axios.get(`/api/front/user/${id}`, { params: { page } });
  },
  fetchAnnounces(page = 1) {
    return axios.get('/api/front/announces', { params: { page } });
  },
  fetchPages(page = 1) {
    return axios.get('/api/front/pages', { params: { page } });
  },
  fetchRanking(page = 1) {
    return axios.get('/api/front/ranking', { params: { page } });
  },
  fetchSearch(word, page = 1) {
    return axios.get('/api/front/search', { params: { word, page } });
  },
  // show
  fetchArticle(slug) {
    return axios.get(`/api/front/articles/${slug}`);
  },
  fetchTags() {
    return axios.get('/api/front/tags');
  },
});

export const useMypageApi = () => ({
  // auth
  getCsrf() {
    return axios.get('/sanctum/csrf-cookie');
  },
  postLogin(params) {
    return axios.post('/login', params);
  },
  postLogout() {
    return axios.post('/api/logout');
  },
  resend() {
    return axios.post('/api/email/resend');
  },
  forget(params) {
    return axios.post('/api/password/email', params);
  },
  reset(params) {
    return axios.post('/api/email/reset', params);
  },
  verify(userId, hash, expires, signature) {
    return axios.get(`/api/email/verify/${userId}/${hash}`, {
      params: { expires, signature },
    });
  },

  // user profile
  fetchUser() {
    return axios.get('/api/mypage/user');
  },
  updateUser(user) {
    return axios.post('/api/mypage/user', { user });
  },

  // article
  fetchArticles() {
    return axios.get('/api/mypage/articles');
  },
  createArticle(params) {
    return axios.post('/api/mypage/articles', params);
  },
  updateArticle(params) {
    return axios.post(`/api/mypage/articles/${params.article.id}`, params);
  },
  // article options
  fetchOptions() {
    return axios.get('/api/mypage/options');
  },

  // tag
  fetchTags(name) {
    return axios.get(`/api/mypage/tags?name=${name}`);
  },
  storeTag(name) {
    return axios.post('/api/mypage/tags', { name });
  },
  updateTag(id, description) {
    return axios.post(`/api/mypage/tags/${id}`, { description });
  },

  // attachments
  fetchAttachments() {
    return axios.get('/api/mypage/attachments');
  },
  storeAttachment(form) {
    return axios.post('/api/mypage/attachments', form, {
      headers: { 'content-type': 'multipart/form-data' },
    });
  },
  deleteAttachment(attachmentId) {
    return axios.delete(`/api/mypage/attachments/${attachmentId}`);
  },
  // analytics
  fetchAnalytics(params) {
    return axios.get('/api/mypage/analytics', { params });
  },

  // 一括DL
  fetchUserBulkZip() {
    return axios.get('/api/mypage/bulk-zip');
  },

  // invitation
  fetchInvites() {
    return axios.get('/api/mypage/invitation_code');
  },
  updateInvitationCode() {
    return axios.post('/api/mypage/invitation_code');
  },
  deleteInvitationCode() {
    return axios.delete('/api/mypage/invitation_code');
  },
  invite(code, params) {
    return axios.post(`/api/mypage/invite/${code}`, params);
  },
});

export const useAdminApi = () => ({
  // auth
  fetchArticles() {
    return axios.get('/api/admin/articles');
  },
  putArticle(id, params) {
    return axios.put(`/api/admin/articles/${id}`, params);
  },
  deleteArticle(id) {
    return axios.delete(`/api/admin/articles/${id}`);
  },
  fetchUsers() {
    return axios.get('/api/admin/users');
  },
  deleteUser(id) {
    return axios.delete(`/api/admin/users/${id}`);
  },
  toggleEditableTag(id) {
    return axios.post(`/api/admin/tags/${id}/toggleEditable`);
  },
  fetchControllOptions() {
    return axios.get('/api/admin/controll_options');
  },
  toggleControllOption(key) {
    return axios.post(`/api/admin/controll_options/${key}/toggle`);
  },
});
