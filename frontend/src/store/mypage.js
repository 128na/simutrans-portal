import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useMypageApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';

const api = useMypageApi();
/**
 * マイページ
 */
export const useMypageStore = defineStore('mypage', () => {
  const articles = ref(null);
  const findArticleById = computed(() => (id) => articles.value?.find((a) => a.id === id));
  const findArticleBySlug = computed(() => (slug) => articles.value?.find((a) => a.slug === slug));
  const articlesReady = computed(() => Array.isArray(articles.value));

  const screenshots = ref(null);
  const screenshotsReady = computed(() => Array.isArray(screenshots.value));
  const findScreenshotById = computed(() => (id) => screenshots.value?.find((a) => a.id === id));
  const screenshotHandler = useApiHandler();
  const fetchScreenshots = () => screenshotHandler.handle({
    doRequest: () => api.fetchScreenshots(),
    done: (res) => { screenshots.value = res.data.data; },
    failedMessage: '添付ファイル一覧取得に失敗しました',
  });

  const attachments = ref(null);
  const attachmentsReady = computed(() => Array.isArray(attachments.value));
  const findAttachmentById = computed(() => (id) => attachments.value?.find((a) => a.id === id));
  const attachmentHandler = useApiHandler();
  const fetchAttachments = () => attachmentHandler.handle({
    doRequest: () => api.fetchAttachments(),
    done: (res) => { attachments.value = res.data.data; },
    failedMessage: '添付ファイル一覧取得に失敗しました',
  });
  const articleHandler = useApiHandler();
  const fetchArticles = () => articleHandler.handle({
    doRequest: () => api.fetchArticles(),
    done: (res) => { articles.value = res.data.data; },
    failedMessage: '記事一覧取得に失敗しました',
  });
  const ready = computed(() => articles.value && attachments.value);

  return {
    ready,

    articles,
    articlesReady,
    findArticleById,
    findArticleBySlug,
    fetchArticles,

    screenshots,
    screenshotsReady,
    findScreenshotById,
    fetchScreenshots,

    attachments,
    attachmentsReady,
    findAttachmentById,
    fetchAttachments,
  };
});
