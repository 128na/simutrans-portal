import { defineStore } from 'pinia';
import { useMypageApi } from 'src/composables/api';
import { useNotify } from 'src/composables/notify';

const api = useMypageApi();
/**
 * マイページ
 */
export const useMypageStore = defineStore('mypage', {
  state: () => ({
    articles: null,
    attachments: null,
    analytics: null,
    tags: null,
  }),
  getters: {
    findArticleById: (state) => (id) => state.articles?.find((a) => a.id === id),
    findArticleBySlug: (state) => (slug) => state.articles?.find((a) => a.slug === slug),
    findAttachmentById: (state) => (id) => state.attachments?.find((a) => a.id === id),
  },
  actions: {
    fetchAttachments() {
      return api.fetchAttachments()
        .then((res) => { this.attachments = res.data.data; })
        .catch(() => {
          const notify = useNotify();
          notify.failedRetryable('添付ファイル一覧取得に失敗しました', this.fetchAttachments);
        });
    },
  },
});
