<template>
  <apexchart type="line" :options="options" :series="series" height="500" />
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
  ANALYTICS_TYPE_DAILY, ANALYTICS_TYPE_MONTHLY, ANALYTICS_TYPE_YEARLY, ANALYTICS_AXIS_DATA_INDEXES, ANALYTICS_AXIS_PV,
} from 'src/const';
import { useQuasar } from 'quasar';

const staticOptions = {
  chart: {
    id: 'article-analytics',
  },
  noData: {
    text: '記事を選択してください',
  },
  // linechartだとtooltip.intersectが動かないのでカスタムで代用する
  // https://github.com/apexcharts/apexcharts.js/issues/2565
  tooltip: {
    shared: false,
    followCursor: true,
    custom({
      series, seriesIndex, dataPointIndex, w,
    }) {
      return `<div class="q-pa-xs"><span>${w.globals.seriesNames[seriesIndex]} : ${series[seriesIndex][dataPointIndex]}</span></div>`;
    },
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
      colors: [({ seriesIndex }) => {
        // pv,cv両方選択時は近しい色に揃える
        if (analytics.axes.length === 2) {
          return seriesIndex % 2 === 0
            ? `hsl(${211 + seriesIndex * 26.5}, 82%, 54%)`
            : `hsl(${211 + (seriesIndex - 1) * 26.5}, 63%, 76%)`;
        }
        // pv,cv一方のときは両方選択時と同じ色にする
        return analytics.axes[0] === ANALYTICS_AXIS_PV
          ? `hsl(${211 + seriesIndex * 53}, 82%, 54%)`
          : `hsl(${211 + seriesIndex * 53}, 63%, 76%)`;
      }],
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
