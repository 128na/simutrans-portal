import axios from 'axios';
const entrypoint = '';
export default {
    // auth
    login(params) {
        return axios.post(`${entrypoint}/api/v2/login`, params);
    },
    logout() {
        return axios.post(`${entrypoint}/api/v2/logout`);
    },
    resend() {
        return axios.post(`${entrypoint}/api/v2/email/resend`);
    },
    register() {
        return axios.post(`${entrypoint}/api/v2/register`);
    },
    reset(params) {
        return axios.post(`${entrypoint}/api/v2/password/email`, params);
    },

    // user profile
    fetchUser() {
        return axios.get(`${entrypoint}/api/v2/mypage/user`);
    },
    updateUser(user) {
        return axios.post(`${entrypoint}/api/v2/mypage/user`, { user });
    },

    // article
    fetchArticles() {
        return axios.get(`${entrypoint}/api/v2/mypage/articles`);
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
        return axios.get(`${entrypoint}/api/v2/mypage/attachments`);
    },
    storeAttachment(form) {
        return axios.post('/api/v2/mypage/attachments/', form, {
            headers: { 'content-type': 'multipart/form-data' }
        });
    },
    deleteAttachment(attachment_id) {
        return axios.delete(`/api/v2/mypage/attachments/${attachment_id}`);
    },
}
