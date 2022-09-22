import api from '../../api';
import { SET_ARTICLES } from '../mutation-types';
import { DateTime } from 'luxon';

export default {
  state: () => {
    return {
      articles: false
    };
  },
  getters: {
    articlesLoaded: state => state.articles !== false,
    articles: state => state.articles || []
  },
  mutations: {
    [SET_ARTICLES](state, articles = false) {
      if (articles) {
        articles = articles.map(a => Object.assign(a, {
          published_at: a.published_at ? DateTime.fromISO(a.published_at) : null,
          modified_at: DateTime.fromISO(a.modified_at),
          created_at: DateTime.fromISO(a.created_at)
        }));
      }
      state.articles = articles;
    }
  },
  actions: {
    /**
     * 投稿記事一覧
     */
    async fetchArticles({ commit, dispatch }) {
      try {
        const res = await api.fetchArticles();
        if (res && res.status === 200) {
          return commit(SET_ARTICLES, res.data.data);
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    /**
     * 記事更新
     */
    async updateArticle({ commit, dispatch }, { params, message = '更新しました' }) {
      dispatch('setApiStatusFetching');
      try {
        const res = await api.updateArticle(params);
        if (res && res.status === 200) {
          dispatch('setApiStatusSuccess');
          commit(SET_ARTICLES, res.data.data);
          return dispatch('setInfoMessage', { message });
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    /**
     * 記事作成
     */
    async createArticle({ commit, dispatch }, { params, message = '作成しました' }) {
      dispatch('setApiStatusFetching');
      try {
        const res = await api.createArticle(params);
        if (res && res.status === 200) {
          dispatch('setApiStatusSuccess');
          commit(SET_ARTICLES, res.data.data);
          return dispatch('setInfoMessage', { message });
        }
        dispatch('setApiStatusError');
      } catch (e) {
        dispatch('setApiStatusError', e);
      }
    },
    clearArticles({ commit }) {
      commit(SET_ARTICLES, false);
    }
  }
};
