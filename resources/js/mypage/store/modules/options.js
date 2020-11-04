import api from '../../api';
import { SET_OPTIONS } from '../mutation-types';

export default {
  state: () => {
    return {
      options: false,
    };
  },
  getters: {
    optionsLoaded: state => state.options !== false,
    options: state => state.options || {},
    getStatusText: state => value => state.options.statuses.find(o => o.value === value).text || '',
  },
  mutations: {
    [SET_OPTIONS](state, options = false) {
      state.options = options;
    },
  },
  actions: {
    /**
     * 記事編集用オプション取得
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
  }
};
