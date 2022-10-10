import { defineStore } from 'pinia';

/**
 * フロント用記事キャッシュ
 */
export const useArticleCacheStore = defineStore('articleCache', {
  state: () => ({ cached: [] }),
  getters: {
    hasCache: (state) => (slug) => state.cached.findIndex((c) => c.slug === slug) !== -1,
    getCache: (state) => (slug) => state.cached.find((c) => c.slug === slug),
  },
  actions: {
    addCache(article) {
      const index = this.cached.findIndex((a) => a.slug === article.slug);
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
