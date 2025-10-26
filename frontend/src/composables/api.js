import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

export const useFrontApi = () => ({
  fetchSearch(word, order, page = 1) {
    return axios.get('/api/front/search', { params: { word, order, page } });
  },
});

export const useMypageApi = () => ({
  // auth
  getCsrf() {
    return axios.get('/sanctum/csrf-cookie');
  },
  postLogin(params) {
    return axios.post('/auth/login', params);
  },
  postLogout() {
    return axios.post('/auth/logout');
  },
  resend() {
    return axios.post('/auth/email/verification-notification');
  },
  verify(userId, hash, expires, signature) {
    return axios.get(`/auth/email/verify/${userId}/${hash}`, {
      params: { expires, signature },
    });
  },
  // two factor
  twoFactorAuthentication() {
    return axios.post('/auth/user/two-factor-authentication');
  },
  twoFactorQrCode() {
    return axios.get('/auth/user/two-factor-qr-code');
  },
  confirmedTwoFactorAuthentication(data) {
    return axios.post('/auth/user/confirmed-two-factor-authentication', data);
  },
  challengeTwoFactorAuthentication(data) {
    return axios.post('/auth/two-factor-challenge', data);
  },
  deleteTwoFactorAuthentication() {
    return axios.delete('/auth/user/two-factor-authentication');
  },
  twoFactorRecoveryCodes() {
    return axios.get('/auth/user/two-factor-recovery-codes');
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

  fetchRedirects() {
    return axios.get('/api/mypage/redirects');
  },
  deleteRedirect(redirectId) {
    return axios.delete(`/api/mypage/redirects/${redirectId}`);
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

  // ログイン履歴
  fetchLoginHistories() {
    return axios.get('/api/mypage/login_histories');
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
