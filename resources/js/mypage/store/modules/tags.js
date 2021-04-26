import api from '../../api';
import { SET_TAGS } from '../mutation-types';

export default {
  state: () => {
    return {
      tags: false,
    };
  },
  getters: {
    tagsLoaded: state => state.tags !== false,
    tags: state => state.tags || [],
  },
  mutations: {
    [SET_TAGS](state, tags = false) {
      state.tags = tags;
    },
  },
  actions: {
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
    clearTags({ commit }) {
      commit(SET_TAGS, false);
    },
  }
};
