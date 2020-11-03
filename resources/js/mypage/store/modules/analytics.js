import api from '../../api'

const SET_ANALYTICS = 'SET_ANALYTICS';

export default {
  state: () => {
    return {
      analytics: [],
    };
  },
  getters: {
    analytics: state => state.analytics || [],
  },
  mutations: {
    [SET_ANALYTICS](state, analytics = false) {
      state.analytics = analytics;
    },
  },
  actions: {
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
};
