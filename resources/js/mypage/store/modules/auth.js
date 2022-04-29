import api from '../../api';
import { SET_INITIALIZED, SET_USER } from '../mutation-types';

export default {
  state: () => {
    return {
      /**
       * 初期化ステータス（ログイン判定完了で初期化完了）
       */
      initialized: false,
      user: false,
    }
  },
  getters: {
    initialized: state => state.initialized,

    user: state => state.user,
    isLoggedIn: state => !!state.user,
    isAdmin: state => state.user && state.user.admin,
    isVerified: state => state.user && state.user.verified,
  },
  mutations: {
    [SET_INITIALIZED](state, initialized = false) {
      state.initialized = initialized
    },
    [SET_USER](state, user = false) {
      state.user = user;
    },
  },
  actions: {
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
    /**
     * ログアウト
     */
    async logout({ commit }) {
      try {
        api.logout();
        commit(SET_USER, null);
      } catch (e) { }
    },
    /**
     * ユーザー登録
     */
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
    /**
     * ユーザープロフィール更新
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
     * PWリセットメール送信
     */
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
     * 招待コード更新
     */
    async updateInvitationCode({ commit, dispatch }, { message = '招待コードを生成しました。' }) {
      dispatch('setApiStatusFetching');
      try {
        const res = await api.updateInvitationCode();
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
     * 招待コード更新
     */
    async deleteInvitationCode({ commit, dispatch }, { message = '招待コードを削除しました。' }) {
      dispatch('setApiStatusFetching');
      try {
        const res = await api.deleteInvitationCode();
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
  }
};
