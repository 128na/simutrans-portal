import api from '../../api';
import { SET_OPTIONS } from '../mutation-types';

export default {
  state: () => {
    return {
      options: false
    };
  },
  getters: {
    optionsLoaded: state => state.options !== false,
    options: state => state.options || {},
    getStatusText: state => value => state.options.statuses.find(o => o.value === value).text || '',
    getCategory: state => id => {
      return state.options.categories.addon.find(c => c.id === id) ||
        state.options.categories.license.find(c => c.id === id) ||
        state.options.categories.page.find(c => c.id === id) ||
        state.options.categories.pak.find(c => c.id === id) ||
        state.options.categories.pak128_position.find(c => c.id === id);
    }
  },
  mutations: {
    [SET_OPTIONS](state, options = false) {
      state.options = options;
    }
  },
  actions: {
    /**
     * 記事編集用オプション取得
     */
    async fetchOptions({ commit, dispatch }) {
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
    clearOptions({ commit }) {
      commit(SET_OPTIONS, false);
    }
  }
};
