import api from '../../api'

const SET_OPTIONS = 'SET_OPTIONS';

export default {
  state: () => {
    return {
      options: false,
    };
  },
  getters: {
    optionsLoaded: state => state.options !== false,
    options: state => state.options || {},

  },
  mutations: {
    [SET_OPTIONS](state, options = false) {
      state.options = options;
    },
  },
  actions: {
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
  }
};
