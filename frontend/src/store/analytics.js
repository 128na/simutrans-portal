import { DateTime } from 'luxon';
import { defineStore } from 'pinia';
import { useQuasar } from 'quasar';
import { useMypageApi } from 'src/composables/api';
import { useErrorHandler } from 'src/composables/errorHandler';
import {
  ANALYTICS_AXIS_CV, ANALYTICS_AXIS_PV, ANALYTICS_MODE_LINE, ANALYTICS_MODE_SUM, ANALYTICS_TYPE_DAILY, D_FORMAT,
} from 'src/const';
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
  const deselectAll = () => { ids.value = []; };
  const selectAll = (articles) => { ids.value = articles.map((a) => a.id); };
  const idsEmpty = computed(() => ids.value.length === 0);

  const type = ref(ANALYTICS_TYPE_DAILY);
  const axes = ref([ANALYTICS_AXIS_CV, ANALYTICS_AXIS_PV]);
  const mode = ref(ANALYTICS_MODE_LINE);
  const isModeSum = computed(() => mode.value === ANALYTICS_MODE_SUM);

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
      if (ids.value.length < 1) {
        return;
      }
      analyticsData.value = [];
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
    idsEmpty,
    toggleId,
    selected,
    deselectAll,
    selectAll,
    analyticsData,
    type,
    axes,
    mode,
    isModeSum,
    startDate,
    endDate,
    dateRange,
    fetch,
    errorMessage,
  };
});
