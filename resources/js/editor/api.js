import axios from 'axios';
export default {
    fetchArticle() {
        return appdata_article;
    },
    createArticle(article, preview = false) {
        return axios.post(`/api/v2/articles${preview ? '?preview' : ''}`, { article });
    },
    updateArticle(article, preview = false) {
        return axios.post(`/api/v2/articles/${article.id}${preview ? '?preview' : ''}`, { article });
    },
    createPreview(article) {
        return axios.post('/api/v2/articles/preview', { article });
    },

    fetchOptions() {
        return axios.get('/api/v2/options');
    },

    fetchTags(name) {
        return axios.get(`/api/v2/tags?name=${name}`);
    },
    storeTag(name) {
        return axios.post('/api/v2/tags', { name });
    },

    fetchAttachments(article_id = '') {
        return axios.get(`/api/v2/attachments/${article_id}`);
    },
    storeAttachment(form) {
        return axios.post('/api/v2/attachments', form, {
            headers: { 'content-type': 'multipart/form-data' }
        });
    },
    deleteAttachment(attachment_id) {
        return axios.delete(`/api/v2/attachments/${attachment_id}`);
    },
}
