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
  const articlesReady = computed(() => !!articles.value);

  const attachments = ref(null);
  const attachmentsReady = computed(() => !!attachments.value);
  const findAttachmentById = computed(() => (id) => attachments.value?.find((a) => a.id === id));
  const notify = useNotify();
  const fetchAttachments = () => api.fetchAttachments()
    .then((res) => { attachments.value = res.data.data; })
    .catch(() => {
      notify.failedRetryable('添付ファイル一覧取得に失敗しました', fetchAttachments);
    });
  const fetchArticles = () => api.fetchArticles()
    .then((res) => { articles.value = res.data.data; })
    .catch(() => {
      notify.failedRetryable('記事一覧取得に失敗しました', fetchArticles);
    });

  return {
    articles,
    articlesReady,
    findArticleById,
    findArticleBySlug,
    attachments,
    attachmentsReady,
    findAttachmentById,
    fetchAttachments,
    fetchArticles,
  };
});
