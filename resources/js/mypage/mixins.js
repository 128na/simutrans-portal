/**
 * 汎用トーストメッセージ
 */
const toastable = {
    methods: {
        toastInfo(message) {
            this._toast('Info', 'info', message);
        },
        toastSuccess(message) {
            this._toast('Success', 'success', message);

        },
        toastError(message) {
            this._toast('Error', 'danger', message, true);

        },
        toastValidationError(errors) {
            const message = Object.values(errors).map(e => e.join("\n")).join("\n");
            this._toast('Validation Error', 'warning', message, true);

        },
        _toast(title, variant, message, noAutoHide = false) {
            this.$bvToast.toast(message, {
                title,
                variant,
                toaster: 'b-toaster-bottom-right',
                solid: true,
                appendToast: true,
                noAutoHide,
                bodyClass: 'pre-line'
            })
        },
        handleErrorToast(error) {
            const res = error.response;
            switch (res.status) {
                case 401:
                    return this.$router.push({ name: 'login' }).catch(e => { })
                case 403:
                    return this.toastError('No permission');
                case 404:
                    return this.toastError('Not found');
                case 422:
                    return this.toastValidationError(res.data.errors);
                case 429:
                    return this.toastError('Too Many Requests');
                default:
                    console.log(error);
                    return this.toastError('Error');
            }
        }
    }
}

/**
 * メール認証していなければ403ページへ飛ばす
 */
const verifiedable = {
    props: ['user'],
    created() {
        if (!this.user.verified) {
            this.$router.push({ name: 'index' });
        }
    },
}

/**
 * プレビュー
 */
const previewable = {
    data() {
        return {
            preview_window: null
        };
    },
    methods: {
        async setPreview(html) {
            if (!this.preview_window || this.preview_window.closed) {
                this.preview_window = window.open();
            }
            this.preview_window.document.body.innerHTML = html;
        }
    }
};

/**
 * APIエラーハンドリング付き
 */
import api from "./api";
const api_handlable = {
    mixins: [toastable],
    data() {
        return {
            fetching: false,
        }
    },
    methods: {
        async login(params) {
            this.fetching = true;
            const res = await api.login(params).catch(this.handleErrorToast);
            if (res && res.status === 200) {
                await this.setUser(res.data.data);
            }
            this.fetching = false;
        },
        async logout() {
            await api.logout().catch(this.handleErrorToast);
            await this.setUser(null);
        },
        resend() {
            return api.resend().catch(this.handleErrorToast);
        },
        async reset(params) {
            this.fetching = true;
            const res = await api.reset(params).catch(this.handleErrorToast);
            if (res && res.status === 200) {
                this.sent();
            }
            this.fetching = false;
        },
        async fetchUser() {
            this.fetching = true;
            const res = await api.fetchUser().catch(this.handleErrorToast);

            if (res && res.status === 200) {
                await this.setUser(res.data.data);
            }
            this.fetching = false;
        },
        async fetchAttachments() {
            this.fetching = true;
            const res = await api.fetchAttachments().catch(this.handleErrorToast);

            if (res && res.status === 200) {
                await this.setAttachments(res.data.data);
            }
            this.fetching = false;
        },
        async fetchArticles() {
            this.fetching = true;
            const res = await api.fetchArticles().catch(this.handleErrorToast);

            if (res && res.status === 200) {
                await this.setArticles(res.data.data);
            }
            this.fetching = false;
        },
        async fetchOptions() {
            this.fetching = true;
            const res = await api.fetchOptions().catch(this.handleErrorToast);

            if (res && res.status === 200) {
                await this.setOptions(res.data);
            }
            this.fetching = false;
        },
        async storeAttachment(file) {
            this.fetching = true;
            const formData = new FormData();
            formData.append("file", file);
            formData.append("type", this.type);
            if (this.id) {
                formData.append("id", this.id);
            }
            if (this.only_image) {
                formData.append("only_image", true);
            }
            const res = await api
                .storeAttachment(formData)
                .catch(this.handleErrorToast);

            if (res && res.status === 200) {
                await this.setAttachments(res.data.data);
            }
            this.fetching = false;
        },
        async deleteAttachment(id) {
            this.fetching = true;
            const res = await api.deleteAttachment(id).catch(this.handleErrorToast);

            if (res && res.status === 200) {
                await this.setAttachments(res.data.data);
            }
            this.fetching = false;
        },
        async fetchTags() {
            this.fetching = true;
            const res = await api.fetchTags(this.search).catch(this.handleErrorToast);

            if (res && res.status === 200) {
                await this.setTags(res.data);
            }
            this.fetching = false;
        },
        async storeTag(name) {
            this.fetching = true;
            const res = await api
                .storeTag(name)
                .catch(this.handleErrorToast);

            if (res && res.status === 201) {
                await this.setTags(res.data);
            }
            this.fetching = false;
        },
        async updateUser(user) {
            this.fetching = true;
            const res = await api.updateUser(user).catch(this.handleErrorToast);

            if (res && res.status === 200) {
                await this.setUser(res.data.data);
            }
            this.fetching = false;
        },
        async createArticle(params) {
            this.fetching = true;
            const res = await api
                .createArticle(params)
                .catch(this.handleErrorToast);
            if (res && res.status === 200) {
                params.preview ? this.setPreview(res.data) : this.setArticles(res.data.data);
            }
            this.fetching = false;
        },
        async updateArticle(params) {
            this.fetching = true;
            const res = await api
                .updateArticle(params)
                .catch(this.handleErrorToast);
            if (res && res.status === 200) {
                params.preview ? this.setPreview(res.data) : this.setArticles(res.data.data);
            }
            this.fetching = false;
        },
        async fetchAnalytics(params) {
            this.fetching = true;
            const res = await api
                .fetchAnalytics(params)
                .catch(this.handleErrorToast);
            if (res && res.status === 200) {
                await this.setAnalytics(res.data.data);
            }
            this.fetching = false;
        }
    },
}

/**
 * ルーターリンク先
 */
const linkable = {
    computed: {
        to_login() {
            return {
                name: "login"
            };
        },
        to_register() {
            return { name: "register" };
        },
        to_reset() {
            return { name: "reset" };
        },
        to_index() {
            return {
                name: "index"
            };
        },
        to_analytics() {
            return {
                name: "analyticsArticle"
            };
        },
        to_addon_post() {
            return {
                name: "createArticle",
                params: { post_type: "addon-post" }
            };
        },
        to_addon_introduction() {
            return {
                name: "createArticle",
                params: { post_type: "addon-introduction" }
            };
        },
        to_page() {
            return {
                name: "createArticle",
                params: { post_type: "page" }
            };
        },
        to_profile() {
            return {
                name: "editProfile"
            };
        },
    }
}

const analytics_constants = {
    computed: {
        TYPE_DAILY: () => "daily",
        TYPE_MONTHLY: () => "monthly",
        TYPE_YEARLY: () => "yearly",

        MODE_LINE: () => 'line',
        MODE_SUM: () => 'sum',

        AXIS_VIEW: () => 'pv',
        AXIS_CONVERSION: () => 'cv',

        INDEX_OF_ARCHIVE_ID: () => 0,
        INDEX_OF_VIEW: () => 1,
        INDEX_OF_CONVERSION: () => 2,
    }
}
export { toastable, previewable, verifiedable, api_handlable, linkable, analytics_constants };
