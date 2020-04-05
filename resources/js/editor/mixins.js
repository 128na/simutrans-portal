const attachments_updatable = {
    methods: {
        handleAttachmentsUpdated(attachments) {
            this.$emit("attachmentsUpdated", attachments);
        }
    }
};
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


export { attachments_updatable, toastable };
