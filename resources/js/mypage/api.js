import axios from 'axios';
const entrypoint = '';
export default {
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
    createArticle(article, preview = false) {
        return axios.post(`/api/v2/mypage/articles${preview ? '?preview' : ''}`, { article });
    },
    updateArticle(article, preview = false) {
        return axios.post(`/api/v2/mypage/articles/${article.id}${preview ? '?preview' : ''}`, { article });
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
