import api from '../../api';
import { SET_BOOKMARKS } from '../mutation-types';

export default {
  state: () => {
    return {
      bookmarks: false
    };
  },
  getters: {
    bookmarksLoaded: state => state.bookmarks !== false,
    bookmarks: state => state.bookmarks || []
  },
  mutations: {
    [SET_BOOKMARKS](state, bookmarks = false) {
      state.bookmarks = bookmarks;
    }
  },
  actions: {
    /**
     * ブックマーク一覧
     */
    async fetchBookmarks({ commit, dispatch }) {
      try {
        const res = await api.fetchBookmarks();
        if (res && res.status === 200) {
          return commit(SET_BOOKMARKS, res.data.data);
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    /**
     * ブックマーク登録
     */
    async storeBookmark({ dispatch, commit }, { params, message = '作成しました' }) {
      dispatch('setApiStatusFetching');

      try {
        const res = await api.storeBookmark(params);

        if (res && res.status === 200) {
          dispatch('setApiStatusSuccess');
          commit(SET_BOOKMARKS, res.data.data);
          return dispatch('setInfoMessage', { message });
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    /**
     * ブックマーク登録
     */
    async updateBookmark({ dispatch, commit }, { params, message = '更新しました' }) {
      dispatch('setApiStatusFetching');

      try {
        const res = await api.updateBookmark(params);
        if (res && res.status === 200) {
          dispatch('setApiStatusSuccess');
          commit(SET_BOOKMARKS, res.data.data);
          return dispatch('setInfoMessage', { message });
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    /**
     * ブックマーク削除
     */
    async deleteBookmark({ dispatch, commit }, { bookmarkId, message = '削除しました' }) {
      dispatch('setApiStatusFetching');

      try {
        const res = await api.deleteBookmark(bookmarkId);

        if (res && res.status === 200) {
          dispatch('setApiStatusSuccess');
          commit(SET_BOOKMARKS, res.data.data);
          return dispatch('setInfoMessage', { message });
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    clearBookmarks({ commit }) {
      commit(SET_BOOKMARKS, false);
    }
  }
};
