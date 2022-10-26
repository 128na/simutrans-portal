<template>
  <apexchart type="line" :options="options" :series="series" />
</template>
<script>
import { useMypageStore } from 'src/store/mypage';
import { useAnalyticsStore } from 'src/store/analytics';
import { defineComponent, computed } from 'vue';
import { DateTime, Interval } from 'luxon';
import {
  D_FORMAT, M_FORMAT, Y_FORMAT,
  ANALYTICS_TYPE_DAILY, ANALYTICS_TYPE_MONTHLY, ANALYTICS_TYPE_YEARLY,
} from 'src/const';

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
    const options = computed(() => ({
      chart: { id: 'article-analytics' },
      xaxis: { categories: categories.value },
    }));
    const series = computed(() => analytics.analyticsData.map((ad) => ({
      name: mypage.findArticleById(Number(ad[0]))?.title || '???',
      data: categories.value.map((c) => ad[1][c.replaceAll('/', '')] || 0),
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
