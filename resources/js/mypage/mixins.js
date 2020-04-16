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
    _toast(title, variant, message, noAutoHide = false) {
      this.$bvToast.toast(this.$t(message), {
        title: this.$t(title),
        variant,
        toaster: 'b-toaster-bottom-right',
        solid: true,
        appendToast: true,
        noAutoHide,
        bodyClass: 'pre-line'
      })
    },
  }
};

/**
 * メール認証していなければログインページへ飛ばす
 */
const verifiedable = {
  props: ['user'],
  created() {
    if (!this.user.verified) {
      this.$router.push({ name: 'index' });
    }
  },
};

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
      errors: {},
    }
  },
  methods: {
    beforeRequest() {
      this.fetching = true;
      this.errors = {};
    },
    afterRequest() {
      this.fetching = false;
    },
    handleError(error) {
      const res = error.response;
      switch (res.status) {
        case 401:
          return this.$router.push({ name: 'login' }).catch(e => { });
        case 403:
          return this.toastError('Forbidden');
        case 404:
          return this.toastError('Not found');
        case 422:
          return this.handleValidationError(res.data.errors);
        case 429:
          return this.toastError('Too Many Requests');
        default:
          console.log(error);
          return this.toastError('Whoops, something went wrong on our servers.');
      }
    },
    handleValidationError(errors) {
      this.errors = errors;
      const message = Object.values(errors).map(e => e.join("\n")).join("\n");
      this._toast('Confirmation required', 'danger', message, true);
    },
    async login(params) {
      this.beforeRequest();
      const res = await api.login(params).catch(this.handleError);
      if (res && res.status === 200) {
        await this.setUser(res.data.data);
      }
      this.afterRequest();
    },
    async logout() {
      await api.logout().catch(this.handleError);
      await this.setUser(null);
    },
    async register(params) {
      this.beforeRequest();
      const res = await api.register(params).catch(this.handleError);
      if (res && res.status === 201) {
        await this.setUser(res.data.data);
      }
      this.afterRequest();
    },
    resend() {
      return api.resend().catch(this.handleError);
    },
    async reset(params) {
      this.beforeRequest();
      const res = await api.reset(params).catch(this.handleError);
      if (res && res.status === 200) {
        this.sent();
      }
      this.afterRequest();
    },
    async fetchUser() {
      this.beforeRequest();
      const res = await api.fetchUser().catch(this.handleError);

      if (res && res.status === 200) {
        await this.setUser(res.data.data);
      }
      this.afterRequest();
    },
    async fetchAttachments() {
      this.beforeRequest();
      const res = await api.fetchAttachments().catch(this.handleError);

      if (res && res.status === 200) {
        await this.setAttachments(res.data.data);
      }
      this.afterRequest();
    },
    async fetchArticles() {
      this.beforeRequest();
      const res = await api.fetchArticles().catch(this.handleError);

      if (res && res.status === 200) {
        await this.setArticles(res.data.data);
      }
      this.afterRequest();
    },
    async fetchOptions() {
      this.beforeRequest();
      const res = await api.fetchOptions().catch(this.handleError);

      if (res && res.status === 200) {
        await this.setOptions(res.data);
      }
      this.afterRequest();
    },
    async storeAttachment(file) {
      this.beforeRequest();
      const formData = new FormData();
      formData.append("file", file);
      formData.append("type", this.type);
      if (this.id) {
        formData.append("id", this.id);
      }
      formData.append("only_image", this.only_image ? 1 : 0);

      const res = await api
        .storeAttachment(formData)
        .catch(this.handleError);

      if (res && res.status === 200) {
        await this.setAttachments(res.data.data);
      }
      this.afterRequest();
    },
    async deleteAttachment(id) {
      this.beforeRequest();
      const res = await api.deleteAttachment(id).catch(this.handleError);

      if (res && res.status === 200) {
        await this.setAttachments(res.data.data);
      }
      this.afterRequest();
    },
    async fetchTags() {
      this.beforeRequest();
      const res = await api.fetchTags(this.search).catch(this.handleError);

      if (res && res.status === 200) {
        await this.setTags(res.data);
      }
      this.afterRequest();
    },
    async storeTag(name) {
      this.beforeRequest();
      const res = await api
        .storeTag(name)
        .catch(this.handleError);

      if (res && res.status === 201) {
        await this.setTags(res.data);
      }
      this.afterRequest();
    },
    async updateUser(user) {
      this.beforeRequest();
      const res = await api.updateUser(user).catch(this.handleError);

      if (res && res.status === 200) {
        await this.setUser(res.data.data);
      }
      this.afterRequest();
    },
    async createArticle(params) {
      this.beforeRequest();
      const res = await api
        .createArticle(params)
        .catch(this.handleError);
      if (res && res.status === 200) {
        params.preview ? this.setPreview(res.data) : this.setArticles(res.data.data);
      }
      this.afterRequest();
    },
    async updateArticle(params) {
      this.beforeRequest();
      const res = await api
        .updateArticle(params)
        .catch(this.handleError);
      if (res && res.status === 200) {
        params.preview ? this.setPreview(res.data) : this.setArticles(res.data.data);
      }
      this.afterRequest();
    },
    async fetchAnalytics(params) {
      this.beforeRequest();
      const res = await api
        .fetchAnalytics(params)
        .catch(this.handleError);
      if (res && res.status === 200) {
        await this.setAnalytics(res.data.data);
      }
      this.afterRequest();
    }
  },
};

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
    to_markdown() {
      return {
        name: "createArticle",
        params: { post_type: "markdown" }
      };
    },
    to_profile() {
      return {
        name: "editProfile"
      };
    },
  }
}

/**
 * エディタを離れる際の確認ダイアログ
 */
const editor_handlable = {
  data() {
    return {
      copy: null,
      has_changed: false,
    };
  },
  watch: {
    copy: {
      deep: true,
      handler(value) {
        if (!this.has_changed) {
          const original = this.getOriginal();
          const changed = JSON.stringify(original) !== JSON.stringify(value);
          if (changed) {
            this.setUnloadDialog();
          }
        }
      }
    }
  },
  methods: {
    setCopy(item) {
      this.copy = JSON.parse(JSON.stringify(item));
    },
    setUnloadDialog() {
      this.has_changed = true;
      window.addEventListener('beforeunload', this._unloadDialogEvent);
    },
    unsetUnloadDialog() {
      this.has_changed = false;
      window.removeEventListener('beforeunload', this._unloadDialogEvent);
    },
    _unloadDialogEvent(e) {
      e.preventDefault();
      e.returnValue = this.$t('Exit without saving?');
    }
  },
  beforeRouteLeave(to, from, next) {
    if (this.has_changed) {
      return next(window.confirm(this.$t('Exit without saving?')));
    }
    return next();
  }
};

/**
 * 分析用の定数
 */
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
    OPTIONS() {
      return {
        types: [
          { value: "daily", text: this.$t("Daily") },
          { value: "monthly", text: this.$t("Monthly") },
          { value: "yearly", text: this.$t("Yearly") }
        ],
        modes: [
          { value: "line", text: this.$t("Transition") },
          { value: "sum", text: this.$t("Total") }
        ],
        axes: [
          { value: "pv", text: this.$t("Page Views") },
          { value: "cv", text: this.$t("Conversions") }
        ]
      }
    }
  }
};

/**
 * バリデーションのステート表示用
 * バリデーションステートの種類
 *      null  : 未実施
 *      true  : 成功
 *      false : 失敗
 */
const validatable = {
  props: {
    errors: {
      default: () => { return {} }
    }
  },
  methods: {
    state(key) {
      return this.errors[key] ? false : null;
    }
  }
};

export { toastable, previewable, verifiedable, api_handlable, linkable, analytics_constants, editor_handlable, validatable };
