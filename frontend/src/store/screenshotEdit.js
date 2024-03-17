import { defineStore } from 'pinia';
import { ref } from 'vue';
import { useMypageApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';

export const useScreenshotEditStore = defineStore('screenshotEdit', () => {
  const screenshot = ref(null);
  const createScreenshot = () => {
    screenshot.value = JSON.parse(JSON.stringify({
      title: '',
      description: '',
      status: 'Private',
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
  const updateUser = async () => {
    const res = await handler.handleWithValidate({
      doRequest: () => null,
      done: () => { },
      successMessage: '保存しました',
    });

    return res.data.data;
  };

  const vali = (key) => handler.getValidationErrorByKey(key);

  return {
    screenshot,
    createScreenshot,
    selectScreenshot,
    vali,
  };
});
