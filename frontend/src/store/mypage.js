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
  },
  actions: {
  },
});
