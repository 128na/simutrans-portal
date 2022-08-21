import api from '../../api';
import { SET_ATTACHMENTS } from '../mutation-types';

export default {
  state: () => {
    return {
      attachments: false
    };
  },
  getters: {
    attachmentsLoaded: state => state.attachments !== false,
    attachments: state => state.attachments || [],
    findAttachment: state => id => state.attachments.find(a => a.id == id)
  },
  mutations: {
    [SET_ATTACHMENTS](state, attachments = false) {
      state.attachments = attachments;
    }
  },
  actions: {
    /**
     * ファイル一覧
     */
    async fetchAttachments({ commit, dispatch }) {
      try {
        const res = await api.fetchAttachments();
        if (res && res.status === 200) {
          return commit(SET_ATTACHMENTS, res.data.data);
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    /**
     * ファイル投稿
     */
    async storeAttachment({ commit, dispatch }, { file, type, id = null, onlyImage = false }) {
      dispatch('setApiStatusFetching');
      try {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('type', type);
        if (id) {
          formData.append('id', id);
        }
        formData.append('only_image', onlyImage ? 1 : 0);

        const res = await api.storeAttachment(formData);
        if (res && res.status === 200) {
          commit(SET_ATTACHMENTS, res.data.data);
          return dispatch('setApiStatusSuccess');
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    /**
     * ファイル削除
     */
    async deleteAttachment({ commit, dispatch }, id) {
      dispatch('setApiStatusFetching');
      try {
        const res = await api.deleteAttachment(id);
        if (res && res.status === 200) {
          commit(SET_ATTACHMENTS, res.data.data);
          return dispatch('setApiStatusSuccess');
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    clearAttachments({ commit }) {
      commit(SET_ATTACHMENTS, false);
    }
  }
};
