<template>
  <div>
    <button-back />
    <h1>Analytics</h1>
    <analytics-graph :datasets="datasets" :labels="labels" />
    <b-button varant="primary" @click="handleApply">Apply</b-button>
    <form-analytics-config v-model="options" />
    <analytics-table :articles="articles" v-model="ids" />
  </div>
</template>
<script>
import { DateTime } from "luxon";
import Interval from "luxon/src/interval.js";
import { api_handlable, analytics_constants } from "../../mixins";
export default {
  props: ["articles"],
  mixins: [api_handlable, analytics_constants],
  data() {
    return {
      ids: [],
      options: {
        type: "daily", // 集計区分 日間、月間、年間
        mode: "line", // 集計方法 推移、積算
        axes: ["pv"], // 集計方法 推移、積算
        start_date: null, // 開始日
        end_date: null // 終了日
      },
      analytics: [],
      datasets: null,
      labels: null
    };
  },
  created() {
    this.initialize();
  },
  computed: {
    format_type() {
      switch (this.options.type) {
        case this.TYPE_DAILY:
          return "yyyyLLdd";
        case this.TYPE_MONTHLY:
          return "yyyyLL";
        case this.TYPE_YEARLY:
          return "yyyy";
      }
    },
    interval_type() {
      switch (this.options.type) {
        case this.TYPE_DAILY:
          return { days: 1 };
        case this.TYPE_MONTHLY:
          return { months: 1 };
        case this.TYPE_YEARLY:
          return { years: 1 };
      }
    },
    interval() {
      return Interval.fromDateTimes(
        this.options.start_date,
        this.options.end_date
      ).splitBy(this.interval_type);
    }
  },
  methods: {
    initialize() {
      this.options.start_date = DateTime.local().minus({ month: 3 });
      this.options.end_date = DateTime.local();
    },
    handleApply() {
      const params = {
        ids: this.ids,
        type: this.options.type,
        start_date: this.options.start_date.toISODate(),
        end_date: this.options.end_date.toISODate()
      };
      this.fetchAnalytics(params);
    },
    setAnalytics(analytics) {
      this.analytics = analytics;

      this.calcLabels();
      this.calcDatasets();
    },
    calcLabels() {
      this.labels = this.interval.map(d => d.start.toFormat(this.format_type));
    },
    calcDatasets() {
      const datasets = this.options.axes
        .map(axis => {
          const axis_index =
            axis === this.AXIS_VIEW
              ? this.INDEX_OF_VIEW
              : this.INDEX_OF_CONVERSION;

          return this.analytics.map(analytic => {
            const article = this.articles.find(
              a => a.id == analytic[this.INDEX_OF_ARCHIVE_ID]
            );
            const values = analytic[axis_index];
            return {
              type: "line",
              label: `${article.title}(${this.axisLabel(axis)})`,
              backgroundColor: this.getColor(article, axis, ".5"),
              borderColor: this.getColor(article, axis),
              borderWidth: 2,
              pointRadius: 1,
              fill: false,
              data: this.calcData(article.created_at, values)
            };
          });
        })
        .flat();
      this.datasets = datasets.length ? datasets : null;
    },
    sumFromOldest(created_at, values) {
      console.log(created_at, this.options.start_date);
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
            d => values[d.start.toFormat(this.format_type)] || 0
          );
        case this.MODE_SUM:
          let total = this.sumFromOldest(created_at, values);

          return this.interval
            .map(d => values[d.start.toFormat(this.format_type)] || 0)
            .map(c => {
              total += c;
              return total;
            });
      }
    },
    axisLabel(axis) {
      switch (axis) {
        case this.AXIS_VIEW:
          return "PV";
        case this.AXIS_CONVERSION:
          return "CV";
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
