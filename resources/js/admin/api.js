import axios from 'axios';
axios.defaults.baseURL = process.env.NODE_ENV === 'production' ? process.env.MIX_APP_URL : '';
axios.defaults.timeout = 5000;
export default {
  fetchUser() {
    return axios.get(`/api/v2/mypage/user`);
  },
  // auth
  flushCache() {
    return axios.post(`/api/v2/admin/flush-cache`);
  },
  debug(level = 'error') {
    return axios.get(`/api/v2/admin/debug/${level}`);
  },
  phpinfo() {
    return axios.get(`/api/v2/admin/phpinfo`);
  },
  fetchUsers() {
    return axios.get(`/api/v2/admin/users`);
  },
  deleteUser(id) {
    return axios.delete(`/api/v2/admin/users/${id}`);
  },
  fetchArticles() {
    return axios.get(`/api/v2/admin/articles`);
  },
  updateArticle(id, params) {
    return axios.put(`/api/v2/admin/articles/${id}`, params);
  },
  deleteArticle(id) {
    return axios.delete(`/api/v2/admin/articles/${id}`);
  },
}
