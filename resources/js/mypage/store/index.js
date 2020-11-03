import Vue from 'vue';
import Vuex from 'vuex'
import api from '../api'
import { DateTime } from "luxon";
Vue.use(Vuex);

const SET_INITIALIZED = 'SET_INITIALIZED';
const SET_INFO_MESSAGE = 'SET_INFO_MESSAGE';
const SET_API_STATUS = 'SET_API_STATUS';
const SET_USER = 'SET_USER';
const SET_ARTICLES = 'SET_ARTICLES';
const SET_OPTIONS = 'SET_OPTIONS';
const SET_ATTACHMENTS = 'SET_ATTACHMENTS';
const SET_TAGS = 'SET_TAGS';
const SET_ANALYTICS = 'SET_ANALYTICS';

export default new Vuex.Store({
  state: {
    /**
     * 初期化ステータス（ログイン判定完了で初期化完了）
     */
    initialized: false,
    /**
     * 通知メッセージ
     */
    info_message: null,
    /**
     * ログイン、保存など状態をユーザーへ知らせる必要のある操作用
     */
    api_status: {
      fetching: false,
      errors: null,
      message: null,
      status_code: null,
    },
    /**
     * 各種データ。false:未ロード
     */
    user: false,
    articles: false,
    options: false,
    attachments: false,
    tags: false,
    analytics: [],
  },
  getters: {
    initialized: state => state.initialized,
    infoMessage: state => state.info_message,
    // api
    fetching: state => state.api_status.fetching,
    statusCode: state => state.api_status.status_code,
    errorMessage: state => state.api_status.message,
    errors: state => state.api_status.errors,
    hasError: state => !!state.api_status.errors,
    /**
     * null: 未判定
     * true: バリエーション通過
     * false: バリデーションエラー
     */
    validationState: state => (key) => {
      if (state.api_status.status_code === 422 && state.api_status.errors && state.api_status.errors[key]) {
        return false;
      }
      return null;
    },
    // user
    user: state => state.user,
    isLoggedIn: state => !!state.user,
    isAdmin: state => state.user && state.user.admin,
    isVerified: state => state.user && state.user.verified,

    // article
    articlesLoaded: state => state.articles !== false,
    articles: state => state.articles || [],
    // option
    optionsLoaded: state => state.options !== false,
    options: state => state.options || {},
    // attachment
    attachmentsLoaded: state => state.attachments !== false,
    attachments: state => state.attachments || [],
    // tag
    tagsLoaded: state => state.tags !== false,
    tags: state => state.tags || [],
    // analytic
    analyticsLoaded: state => state.analytics !== false,
    analytics: state => state.analytics || [],
  },
  mutations: {
    [SET_INITIALIZED](state, initialized = false) {
      state.initialized = initialized
    },
    [SET_INFO_MESSAGE](state, message = null) {
      state.info_message = message;
    },
    [SET_API_STATUS](state, { fetching = false, errors = null, message = null, status_code = 0 }) {
      state.api_status.fetching = fetching;
      state.api_status.errors = errors;
      state.api_status.message = message;
      state.api_status.status_code = status_code;
    },
    [SET_USER](state, user = false) {
      state.user = user;
    },
    [SET_ARTICLES](state, articles = false) {
      if (articles) {
        articles = articles.map(a => Object.assign(a, {
          created_at: DateTime.fromISO(a.created_at),
          updated_at: DateTime.fromISO(a.updated_at),
        }));
      }
      state.articles = articles;
    },
    [SET_OPTIONS](state, options = false) {
      state.options = options;
    },
    [SET_ATTACHMENTS](state, attachments = false) {
      state.attachments = attachments;
    },
    [SET_TAGS](state, tags = false) {
      state.tags = tags;
    },
    [SET_ANALYTICS](state, analytics = false) {
      state.analytics = analytics;
    },
  },
  actions: {
    setInfoMessage({ commit, state }, { message = null, timeout = 5 }) {
      commit(SET_INFO_MESSAGE, message);
      // メッセージが残っていれば消す
      if (timeout) {
        setTimeout(() => {
          if (state.info_message === message) {
            commit(SET_INFO_MESSAGE);
          }
        }, timeout * 1000);
      }
    },
    // api共通処理
    setApiStatusInit({ commit }) {
      commit(SET_API_STATUS, {});
    },
    setApiStatusFetching({ commit }) {
      commit(SET_API_STATUS, { fetching: true });
    },
    setApiStatusSuccess({ commit }) {
      commit(SET_API_STATUS, { status: 200 });
    },
    setApiStatusError({ commit }, error = {}) {
      const res = error.response || null;
      if (!res) {
        return commit(SET_API_STATUS, { message: '通信エラーが発生しました。', status_code: 0 });
      }
      switch (res.status) {
        case 401:
          return commit(SET_API_STATUS, { message: '認証に失敗しました。', status_code: 401 });
        case 403:
          return commit(SET_API_STATUS, { message: '操作を実行できませんでした。', status_code: 403 });
        case 404:
          return commit(SET_API_STATUS, { message: 'データが見つかりませんでした。', status_code: 404 });
        case 419:
          return commit(SET_API_STATUS, { message: 'ページの有効期限が切れました。ページを再読み込みしてから再度操作してください。', status_code: 419 });
        case 422:
          return commit(SET_API_STATUS, {
            message: '入力データを確認してください。',
            errors: res.data.errors,
            status_code: 422
          });
        case 429:
          return commit(SET_API_STATUS, { message: 'リクエスト頻度制限により実行できませんでした。', status_code: 429 });
      }
      return commit(SET_API_STATUS, { message: 'エラーが発生しました', status_code: res.status });
    },

    // 認証系
    /**
     * ユーザーを取得できるかでログイン済みかを判定する。
     * 判定後に初期化ステータスを完了にする
     */
    async checkLogin({ commit }) {
      try {
        const res = await api.fetchUser();
        if (res && res.status === 200) {
          commit(SET_USER, res.data.data);
        }
      } catch (e) { }
      commit(SET_INITIALIZED, true);
    },
    /**
     * ログイン
     */
    async login({ commit, dispatch }, params) {
      dispatch('setApiStatusFetching');
      try {
        const res = await api.login(params);
        if (res && res.status === 200) {
          dispatch('setApiStatusSuccess');
          return commit(SET_USER, res.data.data);
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    async logout({ commit }) {
      try {
        api.logout();
        commit(SET_USER, null);
      } catch (e) { }
    },
    async register({ commit, dispatch }, params) {
      dispatch('setApiStatusFetching');
      try {
        const res = await api.register(params);
        if (res && res.status === 201) {
          commit(SET_USER, res.data.data);
          dispatch('setApiStatusSuccess');
          return dispatch('setInfoMessage', { message: 'ユーザー登録しました。' });
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    async sendResetEmail({ dispatch }, params) {
      dispatch('setApiStatusFetching');
      try {
        const res = await api.reset(params);
        if (res && res.status === 200) {
          dispatch('setApiStatusSuccess');
          return dispatch('setInfoMessage', { message: 'メールを送信しました。' });
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    /**
     * 確認メール送信
     */
    async sendVerifyEmail({ dispatch }) {
      dispatch('setApiStatusFetching');
      try {
        const res = await api.resend();
        if (res && res.status === 200) {
          dispatch('setApiStatusSuccess');
          return dispatch('setInfoMessage', { message: 'メールを送信しました。' });
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },

    /**
     * 記事編集用オプション
     */
    async fetchOptions({ commit }) {
      try {
        const res = await api.fetchOptions();
        if (res && res.status === 200) {
          return commit(SET_OPTIONS, res.data);
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },

    // 記事
    /**
     * 投稿記事一覧
     */
    async fetchArticles({ commit }) {
      try {
        const res = await api.fetchArticles();
        if (res && res.status === 200) {
          return commit(SET_ARTICLES, res.data.data);
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    /**
     * 記事更新
     */
    async updateArticle({ commit, dispatch }, { params, message = '更新しました' }) {
      dispatch('setApiStatusFetching');
      try {
        const res = await api.updateArticle(params);
        if (res && res.status === 200) {
          dispatch('setApiStatusSuccess');
          if (params.preview) {
            return res.data;
          }
          commit(SET_ARTICLES, res.data.data);
          return dispatch('setInfoMessage', { message });
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    /**
     * 記事作成
     */
    async createArticle({ commit, dispatch }, { params, message = '作成しました' }) {
      dispatch('setApiStatusFetching');
      try {
        const res = await api.createArticle(params);
        if (res && res.status === 200) {
          dispatch('setApiStatusSuccess');
          if (params.preview) {
            return res.data;
          }
          commit(SET_ARTICLES, res.data.data);
          return dispatch('setInfoMessage', { message });
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },

    // ファイル
    /**
     * ファイル一覧
     */
    async fetchAttachments({ commit }) {
      try {
        const res = await api.fetchAttachments();
        if (res && res.status === 200) {
          return commit(SET_ATTACHMENTS, res.data.data);
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    /**
     * ファイル投稿
     */
    async storeAttachment({ commit, dispatch }, { file, type, id = null, only_image = false }) {
      dispatch('setApiStatusFetching');
      try {
        const formData = new FormData();
        formData.append("file", file);
        formData.append("type", type);
        if (id) {
          formData.append("id", id);
        }
        formData.append("only_image", only_image ? 1 : 0);

        const res = await api.storeAttachment(formData);
        if (res && res.status === 200) {
          commit(SET_ATTACHMENTS, res.data.data);
          return dispatch('setApiStatusSuccess');
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    /**
     * ファイル削除
     */
    async deleteAttachment({ commit, dispatch }, id) {
      dispatch('setApiStatusFetching');
      try {
        const res = await api.deleteAttachment(id);
        if (res && res.status === 200) {
          commit(SET_ATTACHMENTS, res.data.data);
          return dispatch('setApiStatusSuccess');
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },

    // タグ
    /**
     * タグ検索
     */
    async fetchTags({ commit, dispatch }, name = '') {
      try {
        const res = await api.fetchTags(name);
        if (res && res.status === 200) {
          return commit(SET_TAGS, res.data);
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    /**
     * タグ登録
     */
    async storeTag({ dispatch }, name) {
      try {
        const res = await api.storeTag(name);
        if (res && res.status === 201) {
          return;
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },

    /**
     * ユーザー更新
     */
    async updateUser({ commit, dispatch }, { user, message = 'プロフィールを更新しました。' }) {
      dispatch('setApiStatusFetching');
      try {
        const res = await api.updateUser(user);
        if (res && res.status === 200) {
          commit(SET_USER, res.data.data);
          dispatch('setApiStatusSuccess');
          return dispatch('setInfoMessage', { message });
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    /**
     * 分析データ取得
     */
    async fetchAnalytics({ commit, dispatch }, params) {
      dispatch('setApiStatusFetching');
      try {
        const res = await api.fetchAnalytics(params)
        if (res && res.status === 200) {
          commit(SET_ANALYTICS, res.data.data);
          return dispatch('setApiStatusSuccess');
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    }

  }
})
