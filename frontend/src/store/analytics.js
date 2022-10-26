import { DateTime } from 'luxon';
import { defineStore } from 'pinia';
import { useQuasar } from 'quasar';
import { useMypageApi } from 'src/composables/api';
import { useErrorHandler } from 'src/composables/errorHandler';
import { D_FORMAT } from 'src/const';
import { computed, ref } from 'vue';

export const useAnalyticsStore = defineStore('analytics', () => {
  const ids = ref([]);
  const toggleId = (id) => {
    const index = ids.value.indexOf(id);
    if (index === -1) {
      ids.value.push(id);
    } else {
      ids.value.splice(index, 1);
    }
  };
  const selected = (id) => ids.value.includes(id);
  const deselect = () => { ids.value = []; };

  const type = ref('daily');
  const startDate = ref(DateTime.now().minus({ years: 1 }).toFormat(D_FORMAT));
  const endDate = ref(DateTime.now().toFormat(D_FORMAT));
  const dateRange = computed({
    get: () => ({ from: startDate.value, to: endDate.value }),
    set: ({ from, to }) => {
      startDate.value = from;
      endDate.value = to;
    },
  });

  const api = useMypageApi();
  const { errorMessage, errorHandlerStrict, clearErrorMessage } = useErrorHandler();

  const analyticsData = ref([]);
  const $q = useQuasar();
  const fetch = async () => {
    try {
      $q.loading.show();
      clearErrorMessage();
      const params = {
        ids: ids.value,
        type: type.value,
        start_date: startDate.value,
        end_date: endDate.value,
      };
      const res = await api.fetchAnalytics(params);
      analyticsData.value = res.data.data;
    } catch (error) {
      errorHandlerStrict(error, '解析データ取得に失敗しました');
    } finally {
      $q.loading.hide();
    }
  };

  return {
    ids,
    toggleId,
    selected,
    deselect,
    analyticsData,
    type,
    startDate,
    endDate,
    dateRange,
    fetch,
    errorMessage,
  };
});
