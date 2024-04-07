import { defineStore } from 'pinia';
import { ref } from 'vue';
import { useMypageApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';

export const useScreenshotEditStore = defineStore('screenshotEdit', () => {
  const screenshot = ref(null);
  const shouldNotify = ref(false);
  const createScreenshot = () => {
    screenshot.value = JSON.parse(JSON.stringify({
      title: '',
      description: '',
      status: 'Publish',
      attachments: [],
      links: [],
      articles: [],
    }));
  };
  const selectScreenshot = (select) => {
    screenshot.value = JSON.parse(JSON.stringify(select));
  };

  const api = useMypageApi();
  const handler = useApiHandler();
  const save = async () => {
    const params = {
      screenshot: screenshot.value,
      should_notify: shouldNotify.value,
    };
    const res = await handler.handleWithValidate({
      doRequest: () => (screenshot.value.id
        ? api.updateScreenshot(screenshot.value.id, params)
        : api.storeScreenshot(params)),
      successMessage: '保存しました',
    });

    return res.data.data;
  };
  const destroy = async (id) => {
    const res = await handler.handle({
      doRequest: () => api.deleteScreenshot(id),
      successMessage: '削除しました',
    });

    return res.data.data;
  };

  const vali = (key) => handler.getValidationErrorByKey(key);
  const reset = () => {
    screenshot.value = null;
    handler.clearValidationErrors();
  };

  return {
    shouldNotify,
    screenshot,
    createScreenshot,
    selectScreenshot,
    vali,
    save,
    destroy,
    reset,
  };
});
