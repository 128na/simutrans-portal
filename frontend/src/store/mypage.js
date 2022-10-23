import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useMypageApi } from 'src/composables/api';
import { useNotify } from 'src/composables/notify';

const api = useMypageApi();
/**
 * マイページ
 */
export const useMypageStore = defineStore('mypage', () => {
  const articles = ref(null);
  const findArticleById = computed(() => (id) => articles.value?.find((a) => a.id === id));
  const findArticleBySlug = computed(() => (slug) => articles.value?.find((a) => a.slug === slug));

  const attachments = ref(null);
  const findAttachmentById = computed(() => (id) => attachments.value?.find((a) => a.id === id));
  const fetchAttachments = () => api.fetchAttachments()
    .then((res) => { attachments.value = res.data.data; })
    .catch(() => {
      const notify = useNotify();
      notify.failedRetryable('添付ファイル一覧取得に失敗しました', fetchAttachments);
    });

  return {
    articles,
    findArticleById,
    findArticleBySlug,
    attachments,
    findAttachmentById,
    fetchAttachments,
  };
});
