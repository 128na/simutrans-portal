import { defineStore } from 'pinia';

/**
 * フロント用記事キャッシュ
 */
export const useArticleEditStore = defineStore('articleEdit', {
  state: () => ({
    article: null,
    preview: true,
    tweet: false,
    withoutUpdateModifiedAt: false,
    options: null,
  }),
  getters: {
    ready: (state) => state.article && state.options,
    statuses: (state) => state.options.statuses,
    categories: (state) => state.options.categories,
    postTypes: (state) => state.options.post_types,
    canReservation: (state) => state.article.published_at === null || state.article.status === 'reservation',
    getCategory: (state) => (id) => state.options.categories.addon.find((c) => c.id === id)
      || state.options.categories.license.find((c) => c.id === id)
      || state.options.categories.page.find((c) => c.id === id)
      || state.options.categories.pak.find((c) => c.id === id)
      || state.options.categories.pak128_position.find((c) => c.id === id),
    pak128CategoryId: (state) => {
      const { pak } = state.options.categories;
      return pak.find((c) => c.name === 'Pak128').id || null;
    },
    includesPak128(state) { state.article.categories.some((c) => c.id === this.pak128CategoryId); },
    pak: (state) => state.options.categories.pak.map((c) => Object.create({ label: c.name, value: c.id })),
    addon: (state) => state.options.categories.addon.map((c) => Object.create({ label: c.name, value: c.id })),
    pak128Position: (state) => state.options.categories.pak128_position.map((c) => Object.create({ label: c.name, value: c.id })),
    license: (state) => state.options.categories.license.map((c) => Object.create({ label: c.name, value: c.id })),
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
