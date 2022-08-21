<template>
  <div>
    <page-title>アクセス解析</page-title>
    <page-description>
      投稿した記事のアクセス数やDL、リンククック数の情報を確認できます。<br>
      グラフを右クリックすると画像として保存できます。
    </page-description>
    <div v-if="ready">
      <analytics-graph :datasets="datasets" :labels="labels" />
      <b-form-group>
        <fetching-overlay>
          <b-button varant="primary" @click.prevent="handleApply">
            反映
          </b-button>
        </fetching-overlay>
      </b-form-group>
      <form-analytics-config v-model="options" />
      <analytics-table v-model="ids" :articles="articles">
        <validation-message field="ids" />
      </analytics-table>
    </div>
    <loading-message v-else />
  </div>
</template>
<script>
import { mapGetters, mapActions } from 'vuex';
import { validateVerified } from '../../mixins/auth';

import { DateTime, Interval } from 'luxon';
import { analyticsConstants } from '../../mixins/analytics';
export default {
  mixins: [analyticsConstants, validateVerified],
  data() {
    return {
      ids: [],
      options: {
        type: 'daily', // 集計区分 日間、月間、年間
        mode: 'line', // 集計方法 推移、積算
        axes: ['pv'], // 集計方法 推移、積算
        start_date: null, // 開始日
        end_date: null // 終了日
      },
      datasets: null,
      labels: null
    };
  },
  watch: {
    'options.mode'() {
      this.calcDatasets();
    },
    'options.axes'() {
      this.calcDatasets();
    }
  },
  created() {
    if (this.isVerified) {
      this.initialize();
      if (!this.articlesLoaded) {
        this.fetchArticles();
      }
    }
  },
  computed: {
    ...mapGetters([
      'isVerified',
      'articlesLoaded',
      'articles',
      'analytics',
      'hasError'
    ]),
    format_type() {
      switch (this.options.type) {
        case this.TYPE_DAILY:
          return 'yyyyLLdd';
        case this.TYPE_MONTHLY:
          return 'yyyyLL';
        case this.TYPE_YEARLY:
        default:
          return 'yyyy';
      }
    },
    interval_type() {
      switch (this.options.type) {
        case this.TYPE_DAILY:
          return { days: 1 };
        case this.TYPE_MONTHLY:
          return { months: 1 };
        case this.TYPE_YEARLY:
        default:
          return { years: 1 };
      }
    },
    interval() {
      return Interval.fromDateTimes(
        this.options.start_date,
        this.options.end_date
      ).splitBy(this.interval_type);
    },
    ready() {
      return this.articlesLoaded;
    }
  },
  methods: {
    ...mapActions(['fetchArticles', 'fetchAnalytics']),
    initialize() {
      this.options.start_date = DateTime.local().minus({ month: 3 });
      this.options.end_date = DateTime.local();
    },
    async handleApply() {
      const params = {
        ids: this.ids,
        type: this.options.type,
        start_date: this.options.start_date.toISODate(),
        end_date: this.options.end_date.toISODate()
      };
      await this.fetchAnalytics(params);

      if (!this.hasError) {
        this.calcLabels();
        this.calcDatasets();
      }
      this.scrollToTop();
    },
    setAnalytics(analytics) {
      this.analytics = analytics;
    },
    calcLabels() {
      this.labels = this.interval.map((d) =>
        d.start.toFormat(this.format_type)
      );
    },
    calcDatasets() {
      const datasets = this.options.axes
        .map((axis) => {
          const axis_index =
            axis === this.AXIS_VIEW
              ? this.INDEX_OF_VIEW
              : this.INDEX_OF_CONVERSION;

          return this.analytics.map((analytic) => {
            const article = this.articles.find(
              (a) => a.id == analytic[this.INDEX_OF_ARCHIVE_ID]
            );
            const values = analytic[axis_index];
            return {
              type: 'line',
              label: `${article.title}(${this.axisLabel(axis)})`,
              backgroundColor: this.getColor(article, axis, '.5'),
              borderColor: this.getColor(article, axis),
              borderWidth: 2,
              pointRadius: 1,
              lineTension: 0,
              fill: false,
              data: this.calcData(article.created_at, values)
            };
          });
        })
        .flat();
      this.datasets = datasets.length ? datasets : null;
    },
    sumFromOldest(created_at, values) {
      return Interval.fromDateTimes(created_at, this.options.start_date)
        .splitBy(this.interval_type)
        .reduce((acc, d) => {
          acc += values[d.start.toFormat(this.format_type)] || 0;
          return acc;
        }, 0);
    },
    calcData(created_at, values) {
      switch (this.options.mode) {
        case this.MODE_LINE:
          return this.interval.map(
            (d) => values[d.start.toFormat(this.format_type)] || 0
          );
        case this.MODE_SUM:
          return this.calcSumData(created_at, values);
      }
    },
    calcSumData(created_at, values) {
      let total = this.sumFromOldest(created_at, values);

      return this.interval
        .map((d) => values[d.start.toFormat(this.format_type)] || 0)
        .map((c) => {
          total += c;
          return total;
        });
    },
    axisLabel(axis) {
      switch (axis) {
        case this.AXIS_VIEW:
          return 'PV';
        case this.AXIS_CONVERSION:
          return 'CV';
      }
    },
    getColor(article, axis, alpha = 1) {
      switch (axis) {
        case this.AXIS_VIEW:
          return `hsl(${article.id * 53}, 48%, 47%, ${alpha})`;
        case this.AXIS_CONVERSION:
          return `hsl(${article.id * 53}, 48%, 27%, ${alpha})`;
      }
    }
  }
};
</script>
