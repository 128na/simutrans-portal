import Vue from 'vue';
import Vuex from 'vuex'
import api from '../api'
Vue.use(Vuex);

const SET_API_STATUS = 'SET_API_STATUS';
const SET_USER = 'SET_USER';
const SET_ARTICLES = 'SET_ARTICLES';
const SET_OPTIONS = 'SET_OPTIONS';
const SET_ATTACHMENTS = 'SET_ATTACHMENTS';

export default new Vuex.Store({
  state: {
    api_status: {
      fetching: false,
      errors: null,
      error_message: null,
      status_code: null,
    },
    user: null,
    articles: [],
  },
  getters: {
    // api
    fetching: state => state.api_status.fetching,
    errors: state => state.api_status.errors,
    statusCode: state => state.api_status.status_code,
    errorMessage: state => state.api_status.error_message,
    validationState: state => (key) => {
      if (state.api_status.status_code !== 422) {
        return null;
      }
      return state.api_status.errors && !!state.api_status.errors[key];
    },
    // user
    user: state => state.user,
    isLoggedIn: state => !!state.user,
    isAdmin: state => state.user && state.user.admin,
    isVerified: state => state.user && state.user.verified,

    // article
  },
  mutations: {
    [SET_API_STATUS](state, { fetching = false, errors = null, error_message = null, status_code = 0 }) {
      state.api_status.fetching = fetching;
      state.api_status.errors = errors;
      state.api_status.error_message = error_message;
      state.api_status.status_code = status_code;
    },
    [SET_USER](state, user) {
      state.user = user;
    },
    [SET_ARTICLES](state, articles = []) {
      state.articles = articles;
    },
    [SET_OPTIONS](state, options = []) {
      state.options = options;
    },
    [SET_ATTACHMENTS](state, attachments = []) {
      state.attachments = attachments;
    },
  },
  actions: {
    setApiStatusFetching({ commit }) {
      commit(SET_API_STATUS, { fetching: true });
    },
    setApiStatusSuccess({ commit }) {
      commit(SET_API_STATUS, { status: 200 });
    },
    setApiStatusError({ commit }, error) {
      const res = error.response || null;
      if (!res) {
        return commit(SET_API_STATUS, { error_message: '通信エラーが発生しました。', status_code: 0 });
      }
      switch (res.status) {
        case 401:
          return commit(SET_API_STATUS, { error_message: '認証に失敗しました。', status_code: 401 });
        case 403:
          return commit(SET_API_STATUS, { error_message: '操作を実行できませんでした。', status_code: 403 });
        case 404:
          return commit(SET_API_STATUS, { error_message: 'データが見つかりませんでした。', status_code: 404 });
        case 422:
          return commit(SET_API_STATUS, {
            error_message: res.data.error_message || '入力データを確認してください。',
            errors: res.data.errors,
            status_code: 422
          });
        case 429:
          return commit(SET_API_STATUS, { error_message: 'リクエスト頻度制限により実行できませんでした。', status_code: 429 });
      }
      return commit(SET_API_STATUS, { error_message: 'エラーが発生しました', status_code: res.status });
    },
    /**
     * ログイン
     */
    async login({ dispatch, commit }, params) {
      dispatch('setApiStatusFetching');
      try {
        const res = await api.login(params);
        if (res && res.status === 200) {
          dispatch('setApiStatusSuccess');
          dispatch('fetchArticles');
          dispatch('fetchOptions');
          dispatch('fetchAttachments');
          return commit(SET_USER, res.data.data);
        }
        dispatch('setApiStatusError', {});
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    /**
     * ユーザーを取得できるかでログイン済みかを判定する。
     */
    async checkLogin({ dispatch, commit }) {
      try {
        const res = await api.fetchUser();
        if (res && res.status === 200) {
          commit(SET_USER, res.data.data);
          dispatch('fetchArticles');
          dispatch('fetchOptions');
          dispatch('fetchAttachments');
        }
      } catch (e) {
      }
    },
    async logout({ commit }) {
      try {
        api.logout();
        commit(SET_USER, null);
      } catch (e) { }
    },
    /**
     * 必要なデータを取得する
     */
    async fetchArticles({ commit }) {
      try {
        const res = await api.fetchArticles();
        if (res && res.status === 200) {
          return commit(SET_ARTICLES, res.data.data);
        }
      } catch (e) {
      }
    },
    async fetchOptions({ commit }) {
      try {
        const res = await api.fetchOptions();
        if (res && res.status === 200) {
          return commit(SET_OPTIONS, res.data);
        }
      } catch (e) {
      }
    },
    async fetchAttachments({ commit }) {
      try {
        const res = await api.fetchAttachments();
        if (res && res.status === 200) {
          return commit(SET_ATTACHMENTS, res.data.data);
        }
      } catch (e) {
      }
    },
  }
})
