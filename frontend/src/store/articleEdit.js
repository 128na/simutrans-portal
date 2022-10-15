import { defineStore } from 'pinia';

/**
 * フロント用記事キャッシュ
 */
export const useArticleEditStore = defineStore('articleEdit', {
  state: () => ({
    article: null,
    preview: false,
    tweet: true,
    withoutUpdateModifiedAt: false,
  }),
  getters: {
  },
  actions: {
    setArticle(article) {
      this.state.article = JSON.parse(JSON.stringify(article));
    },
    createAddonPost() {
      console.log('createAddonPost');
    },
    createAddonIntroduction() {
      console.log('createAddonIntroduction');
    },
    createPage() {
      console.log('createPage');
    },
    createMarkdown() {
      console.log('createMarkdown');
    },
  },
});
