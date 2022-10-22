import { defineStore } from 'pinia';

/**
 * マイページ
 */
export const useMypageStore = defineStore('mypage', {
  state: () => ({
    articles: null,
    attachments: null,
    options: null,
    analytics: null,
    tags: null,
  }),
  getters: {
    findArticleById: (state) => (id) => state.articles?.find((a) => a.id === id),
    findArticleBySlug: (state) => (slug) => state.articles?.find((a) => a.slug === slug),
    findAttachmentById: (state) => (id) => state.attachments?.find((a) => a.id === id),
  },
  actions: {
  },
});
