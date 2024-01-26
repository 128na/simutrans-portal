import { defineStore } from 'pinia';

/**
 * フロント用記事キャッシュ
 */
export const useArticleCacheStore = defineStore('articleCache', {
  state: () => ({ cached: [] }),
  getters: {
    hasCache: (state) => (idOrNickname, slug) => {
      if (Number.isNaN(Number(idOrNickname))) {
        return state.cached.findIndex((c) => c.user.nickname === idOrNickname && c.slug === slug) !== -1;
      }
      const id = Number(idOrNickname);
      return state.cached.findIndex((c) => c.user.id === id && c.slug === slug) !== -1;
    },
    getCache: (state) => (idOrNickname, slug) => {
      if (Number.isNaN(Number(idOrNickname))) {
        return state.cached.find((c) => c.user.nickname === idOrNickname && c.slug === slug);
      }
      const id = Number(idOrNickname);
      return state.cached.find((c) => c.user.id === id && c.slug === slug);
    },
  },
  actions: {
    addCache(article) {
      const index = this.cached.findIndex((a) => a.id === article.id);
      if (index === -1) {
        this.cached.push(article);
      } else {
        this.cached.splice(index, 1, article);
      }
    },
    addCaches(articles) {
      articles.map((a) => this.addCache(a));
    },
  },
});
