import axios from 'axios';
const app_url = process.env.MIX_APP_URL;
export default {
    // auth
    login(params) {
        return axios.post(`${app_url}/api/v2/login`, params);
    },
    logout() {
        return axios.post(`${app_url}/api/v2/logout`);
    },
    resend() {
        return axios.post(`${app_url}/api/v2/email/resend`);
    },
    register(params) {
        return axios.post(`${app_url}/api/v2/register`, params);
    },
    reset(params) {
        return axios.post(`${app_url}/api/v2/password/email`, params);
    },

    // user profile
    fetchUser() {
        return axios.get(`${app_url}/api/v2/mypage/user`);
    },
    updateUser(user) {
        return axios.post(`${app_url}/api/v2/mypage/user`, { user });
    },

    // article
    fetchArticles() {
        return axios.get(`${app_url}/api/v2/mypage/articles`);
    },
    createArticle(params) {
        return axios.post(`${app_url}/api/v2/mypage/articles`, params);
    },
    updateArticle(params) {
        return axios.post(`${app_url}/api/v2/mypage/articles/${params.article.id}`, params);
    },
    // article options
    fetchOptions() {
        return axios.get(`${app_url}/api/v2/mypage/options`);
    },

    // tag
    fetchTags(name) {
        return axios.get(`${app_url}/api/v2/mypage/tags?name=${name}`);
    },
    storeTag(name) {
        return axios.post(`${app_url}/api/v2/mypage/tags`, { name });
    },

    // attachments
    fetchAttachments() {
        return axios.get(`${app_url}/api/v2/mypage/attachments`);
    },
    storeAttachment(form) {
        return axios.post(`${app_url}/api/v2/mypage/attachments`, form, {
            headers: { 'content-type': 'multipart/form-data' }
        });
    },
    deleteAttachment(attachment_id) {
        return axios.delete(`${app_url}/api/v2/mypage/attachments/${attachment_id}`);
    },
    // analytics
    fetchAnalytics(params) {
        return axios.get(`${app_url}/api/v2/mypage/analytics`, { params });
    },
}
