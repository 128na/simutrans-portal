import Vue from 'vue';
import Vuex from 'vuex';
import api from '../api';
import { DateTime } from 'luxon';
Vue.use(Vuex);

const SET_INITIALIZED = 'SET_INITIALIZED';
const SET_USER = 'SET_USER';
const SET_USERS = 'SET_USERS';
const SET_ARTICLES = 'SET_ARTICLES';
const SET_PHPINFO = 'SET_PHPINFO';
export default new Vuex.Store({
  modules: {
  },
  state: {
    initialized: false,
    user: false,
    phpinfo: null,
    users: [],
    articles: []
  },
  getters: {
    initialized: state => state.initialized,
    isAdmin: state => state.user && state.user.admin,
    phpinfo: state => state.phpinfo,
    users: state => state.users,
    articles: state => state.articles
  },
  mutations: {
    [SET_INITIALIZED](state, initialized = false) {
      state.initialized = initialized;
    },
    [SET_USER](state, user = false) {
      state.user = user;
    },
    [SET_USERS](state, users = []) {
      state.users = users.map(u => Object.assign(u, {
        email_verified_at: u.email_verified_at ? DateTime.fromISO(u.email_verified_at).toFormat('yyyy/LL/dd HH:mm') : null,
        created_at: u.created_at ? DateTime.fromISO(u.created_at).toFormat('yyyy/LL/dd HH:mm') : null,
        updated_at: u.updated_at ? DateTime.fromISO(u.updated_at).toFormat('yyyy/LL/dd HH:mm') : null,
        deleted_at: u.deleted_at ? DateTime.fromISO(u.deleted_at).toFormat('yyyy/LL/dd HH:mm') : null
      })); ;
    },
    [SET_ARTICLES](state, articles = []) {
      state.articles = articles.map(a => Object.assign(a, {
        created_at: a.created_at ? DateTime.fromISO(a.created_at).toFormat('yyyy/LL/dd HH:mm') : null,
        updated_at: a.updated_at ? DateTime.fromISO(a.updated_at).toFormat('yyyy/LL/dd HH:mm') : null,
        deleted_at: a.deleted_at ? DateTime.fromISO(a.deleted_at).toFormat('yyyy/LL/dd HH:mm') : null
      }));
    },
    [SET_PHPINFO](state, phpinfo) {
      state.phpinfo = phpinfo;
    }
  },
  actions: {
    async initialize({ commit }) {
      const res = await api.fetchUser().then(showResponse).catch(showError);
      if (res && res.status === 200) {
        commit(SET_USER, res.data.data);
      }
      commit(SET_INITIALIZED, true);
    },
    flushCache() {
      api.flushCache().then(showResponse).catch(showError);
    },
    fetchDebug(store, level = 'error') {
      api.debug(level).then(showResponse).catch(showError);
    },
    async fetchPhpinfo({ commit }) {
      const res = await api.phpinfo().then(showResponse).catch(showError);
      if (res && res.status === 200) {
        commit(SET_PHPINFO, res.data);
      }
    },
    async fetchArticles({ commit }) {
      const res = await api.fetchArticles().then(showResponse).catch(showError);
      if (res && res.status === 200) {
        commit(SET_ARTICLES, res.data);
      }
    },
    async updateArticle({ commit }, { id, article }) {
      const res = await api.updateArticle(id, { article }).then(showResponse).catch(showError);
      if (res && res.status === 200) {
        commit(SET_ARTICLES, res.data);
      }
    },
    async deleteArticle({ commit }, id) {
      const res = await api.deleteArticle(id).then(showResponse).catch(showError);
      if (res && res.status === 200) {
        commit(SET_ARTICLES, res.data);
      }
    },
    async fetchUsers({ commit }) {
      const res = await api.fetchUsers().then(showResponse).catch(showError);
      if (res && res.status === 200) {
        commit(SET_USERS, res.data);
      }
    },
    async storeUser({ commit }, params) {
      const res = await api.storeUser(params).then(showResponse).catch(showError);
      if (res && res.status === 200) {
        return true;
      }
      return false;
    },
    async deleteUser({ commit }, id) {
      const res = await api.deleteUser(id).then(showResponse).catch(showError);
      if (res && res.status === 200) {
        commit(SET_USERS, res.data);
      }
    }
  }
});

function showError(error = {}) {
  console.error('api error', error.response);
  return error;
}
function showResponse(response = {}) {
  console.log('api rersponse', { response });
  return response;
}
