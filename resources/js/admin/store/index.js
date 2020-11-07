import Vue from 'vue';
import Vuex from 'vuex';
import api from "../api";
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
    articles: [],
  },
  getters: {
    initialized: state => state.initialized,
    isAdmin: state => state.user && state.user.admin,
    phpinfo: state => state.phpinfo,
    users: state => state.users,
    articles: state => state.articles,
  },
  mutations: {
    [SET_INITIALIZED](state, initialized = false) {
      state.initialized = initialized;
    },
    [SET_USER](state, user = false) {
      state.user = user;
    },
    [SET_USERS](state, users = []) {
      state.users = users;
    },
    [SET_ARTICLES](state, articles = []) {
      state.articles = articles;
    },
    [SET_PHPINFO](state, phpinfo) {
      state.phpinfo = phpinfo;
    },
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
    fetchDebug({ }, level = 'error') {
      api.debug(level).then(showResponse).catch(showError);
    },
    async fetchPhpinfo({ commit }) {
      const res = await api.phpinfo().then(showResponse).catch(showError);
      if (res && res.status === 200) {
        commit(SET_PHPINFO, res.data);
      }
    },
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
