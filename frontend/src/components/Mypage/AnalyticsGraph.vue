<template>
  <apexchart type="line" :options="options" :series="series" />
</template>
<script>
import { useMypageStore } from 'src/store/mypage';
import { useAnalyticsStore } from 'src/store/analytics';
import {
  defineComponent, computed, ref, watch,
} from 'vue';
import { DateTime, Interval } from 'luxon';
import {
  D_FORMAT, M_FORMAT, Y_FORMAT,
  ANALYTICS_TYPE_DAILY, ANALYTICS_TYPE_MONTHLY, ANALYTICS_TYPE_YEARLY, ANALYTICS_AXIS_DATA_INDEXES,
} from 'src/const';
import { useQuasar } from 'quasar';

const staticOptions = {
  chart: {
    id: 'article-analytics',
  },
  noData: {
    text: '記事を選択してください',
  },
};

export default defineComponent({
  name: 'AnylticsGraph',
  components: {},
  setup() {
    const mypage = useMypageStore();
    const analytics = useAnalyticsStore();

    const splitBy = computed(() => {
      switch (analytics.type) {
        case ANALYTICS_TYPE_DAILY:
          return { days: 1 };
        case ANALYTICS_TYPE_MONTHLY:
          return { months: 1 };
        case ANALYTICS_TYPE_YEARLY:
          return { years: 1 };
        default:
          throw new Error('invalid type');
      }
    });
    const format = computed(() => {
      switch (analytics.type) {
        case ANALYTICS_TYPE_DAILY:
          return D_FORMAT;
        case ANALYTICS_TYPE_MONTHLY:
          return M_FORMAT;
        case ANALYTICS_TYPE_YEARLY:
          return Y_FORMAT;
        default:
          throw new Error('invalid type');
      }
    });
    const categories = computed(() => {
      const interval = Interval.fromDateTimes(
        DateTime.fromFormat(analytics.startDate, D_FORMAT),
        DateTime.fromFormat(analytics.endDate, D_FORMAT),
      );
      return interval.splitBy(splitBy.value).map((d) => d.start.toFormat(format.value));
    });
    const $q = useQuasar();
    const dark = ref($q.dark.isActive);
    watch(ref($q.dark), () => {
      dark.value = $q.dark.isActive;
    }, { deep: true });
    const options = computed(() => ({
      ...staticOptions,
      xaxis: { categories: categories.value },
      theme: { mode: dark.value ? 'dark' : 'light' },
    }));
    const series = computed(() => analytics.analyticsData.flatMap((ad) => analytics.axes.map((ax) => {
      const article = mypage.findArticleById(Number(ad[0]));
      const collection = categories.value.map((c) => ad[ANALYTICS_AXIS_DATA_INDEXES[ax]][c.replaceAll('/', '')] || 0);
      return {
        name: `${article?.title || '?'} (${ax})`,
        data: analytics.isModeSum
          ? collection.reduce((prev, current, currentIndex) => {
            prev.push((prev[currentIndex - 1] || 0) + current);
            return prev;
          }, [])
          : collection,
      };
    })));

    return {
      options,
      series,
      mypage,
      analytics,
    };
  },
});
</script>
