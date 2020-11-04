import Vue from 'vue';
import Vuex from 'vuex';

import ArticlesModule from './modules/articles';
import AuthModule from './modules/auth';
import AttachmentsModule from './modules/attachments';
import TagsModule from './modules/tags';
import AnalyticsModule from './modules/analytics';
import OptionsModule from './modules/options';
Vue.use(Vuex);

import { SET_INFO_MESSAGE, SET_API_STATUS } from './mutation-types';

export default new Vuex.Store({
  modules: {
    AuthModule,
    ArticlesModule,
    AttachmentsModule,
    TagsModule,
    OptionsModule,
    AnalyticsModule,
  },
  state: {
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
  },
  getters: {
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
    getValidationErrors: state => key => {
      return state.api_status.errors && state.api_status.errors[key] || [];
    },
  },
  mutations: {
    [SET_INFO_MESSAGE](state, message = null) {
      state.info_message = message;
    },
    [SET_API_STATUS](state, { fetching = false, errors = null, message = null, status_code = 0 }) {
      state.api_status.fetching = fetching;
      state.api_status.errors = errors;
      state.api_status.message = message;
      state.api_status.status_code = status_code;
    },
  },
  actions: {
    setInfoMessage({ commit, state }, { message = null, timeout = 5 }) {
      commit(SET_INFO_MESSAGE, message);
      if (timeout) {
        setTimeout(() => {
          // 指定時間経過後にメッセージが残っていれば消す
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
  }
});
