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

const verifiedable = {
    props: ['user'],
    created() {
        if (!this.user.verified) {
            this.$router.push({ name: 'index' });
        }
    },
}

import api from "./api";
const article_editable = {
    mixins: [toastable],
    data() {
        return {
            article: null,
            should_tweet: true,
            preview_window: null
        };
    },
    created() {
        this.should_tweet = !this.is_edit;
    },
    computed: {
        is_edit() {
            return !!this.$route.params.id;
        }
    },
    methods: {
        async handlePreview() {
            const html = await this.updateOrCreate(true);
            if (html) {
                if (!this.preview_window || this.preview_window.closed) {
                    this.preview_window = window.open();
                }
                this.preview_window.document.body.innerHTML = html;
            }
        },
        async handleUpdateOrCreate() {
            const data = await this.updateOrCreate();
            if (data) {
                this.$emit('update:articles', data.data);
                this.$router.push({ name: 'index' });
            }
        },
        updateOrCreate(preview = false) {
            return this.is_edit ? this.update(preview) : this.create(preview);
        },
        async create(preview) {
            const res = await api
                .createArticle(this.article, preview)
                .catch(this.handleErrorToast);
            if (res && res.status === 200) {
                return res.data;
            }
        },
        async update(preview) {
            const res = await api
                .updateArticle(this.article, preview)
                .catch(this.handleErrorToast);
            if (res && res.status === 200) {
                return res.data;
            }
        }
    }
};

export { toastable, article_editable, verifiedable };
