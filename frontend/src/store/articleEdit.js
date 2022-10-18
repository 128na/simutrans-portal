import { defineStore } from 'pinia';

/**
 * フロント用記事キャッシュ
 */
export const useArticleEditStore = defineStore('articleEdit', {
  state: () => ({
    article: null,
    preview: true,
    tweet: true,
    withoutUpdateModifiedAt: false,
    options: null,
  }),
  getters: {
    ready: (state) => state.article && state.options,
    statuses: (state) => state.options.statuses,
    categories: (state) => state.options.categories,
    postTypes: (state) => state.options.post_types,
    canReservation: (state) => state.article.published_at === null || state.article.status === 'reservation',

  },
  actions: {
    togglePreview() {
      this.preview = !this.preview;
    },
    setArticle(article) {
      this.article = JSON.parse(JSON.stringify(article));
    },
    createAddonPost() {
      this.setArticle({
        post_type: 'addon-post',
        title: '',
        slug: '',
        status: 'draft',
        contents: {
          thumbnail: null,
          author: '',
          description: '',
          file: null,
          license: '',
          thanks: '',
        },
        categories: [],
        tags: [],
        published_at: null,
      });
    },
    createAddonIntroduction() {
      this.setArticle({
        post_type: 'addon-introduction',
        title: '',
        slug: '',
        status: 'draft',
        contents: {
          thumbnail: null,
          agreement: false,
          exclude_link_check: false,
          author: '',
          description: '',
          license: '',
          link: '',
          thanks: '',
        },
        categories: [],
        tags: [],
        published_at: null,
      });
    },
    createPage() {
      this.setArticle({
        post_type: 'page',
        title: '',
        slug: '',
        status: 'draft',
        contents: {
          thumbnail: null,
          sections: [{ type: 'text', text: '' }],
        },
        categories: [],
        published_at: null,
      });
    },
    createMarkdown() {
      this.setArticle({
        post_type: 'markdown',
        title: '',
        slug: '',
        status: 'draft',
        contents: {
          thumbnail: null,
          markdown: '',
        },
        categories: [],
        published_at: null,
      });
    },
  },
});
