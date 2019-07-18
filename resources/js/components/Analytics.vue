<template>
  <div class="analytics">
    <section class="chart-area">
      <line-chart :datasets="datasets" :labels="labels"></line-chart>
    </section>

    <section>
      <b-form-group :label="__('Types')">
        <b-form-radio-group v-model="type" :options="optionTypes"></b-form-radio-group>
      </b-form-group>
    </section>

    <section>
      <b-form-group :label="__('Aggregation method')">
        <b-form-radio-group v-model="mode" :options="optionModes"></b-form-radio-group>
      </b-form-group>
    </section>

    <section>
      <div class="pb-1">{{ __('Term') }}</div>
      <b-form inline>
        <b-form-input v-model="start" type="text"></b-form-input>～
        <b-form-input v-model="end" type="text"></b-form-input>
      </b-form>
    </section>

    <section>
      <b-form-group :label="__('Target')">
        <b-form-checkbox-group v-model="render_types" :options="optionRenderTypes"></b-form-checkbox-group>
      </b-form-group>
    </section>

    <section>
      <h5>{{ __('Articles') }}</h5>
      <table class="table table-bordered">
        <thead>
          <tr class="clickable" @click="toggleAllChecked" :class="{ checked: toggle_all }">
            <th>
              <b-form-checkbox :checked="toggle_all"></b-form-checkbox>
            </th>
            <th>{{ __('Toggle All') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="article in articles"
            :key="article.id"
            @click="toggleChecked(article.id)"
            class="clickable"
            :class="{ checked: article.checked }"
          >
            <td>
              <b-form-checkbox :checked="article.checked"></b-form-checkbox>
            </td>
            <td>{{ article.title }}</td>
          </tr>
        </tbody>
      </table>
    </section>
  </div>
</template>

<script>
import LineChart from "./LineChart";
import { DateTime } from "luxon";
import Interval from "luxon/src/interval.js";
import constValues from "../files/const_values.js";

export default {
  mixins: [constValues],
  components: {
    LineChart
  },
  data() {
    return {
      articles: window.articles,
      type: null, // 集計区分 日間、月間、年間
      mode: null, // 集計方法 推移、積算
      start: null, // 開始日
      end: null, // 終了日
      toggle_all: false, //記事チェック全切替用チェック状態
      render_types: []
    };
  },
  created() {
    this.type = this.TYPE_MONTHLY;
    this.mode = this.MODE_LINE;

    this.start = DateTime.local()
      .minus({ years: 1 })
      .toFormat("yyyyLLdd");
    this.end = DateTime.local().toFormat("yyyyLLdd");

    this.render_types = [this.RENDER_TYPE_VIEW];
  },
  methods: {
    __(key) {
      return window.lang[key] || key;
    },
    toggleChecked(id) {
      this.articles = this.articles.map(a =>
        a.id !== id ? a : Object.assign(a, { checked: !a.checked })
      );
    },
    toggleAllChecked() {
      this.toggle_all = !this.toggle_all;
      this.articles = this.articles.map(a =>
        Object.assign(a, { checked: this.toggle_all })
      );
    },
    getColor(article, render_type, alpha = 1) {
      if (render_type === this.RENDER_TYPE_VIEW) {
        return `hsla(${article.id * 53}, 48%, 47%, ${alpha})`;
      }
      if (render_type === this.RENDER_TYPE_CONVERSION) {
        return `hsla(${article.id * 53}, 48%, 27%, ${alpha})`;
      }
    },
    sumFromOldest(article, render_type) {
      return Interval.fromDateTimes(
        DateTime.fromISO(article.created_at),
        DateTime.fromISO(this.start)
      )
        .splitBy(this.interval_type)
        .reduce((acc, d) => {
          acc += article[render_type][d.start.toFormat(this.period_type)] || 0;
          return acc;
        }, 0);
    },
    calcData(article, render_type) {
      switch (this.mode) {
        case this.MODE_LINE:
          return this.interval.map(
            d => article[render_type][d.start.toFormat(this.period_type)] || 0
          );
        case this.MODE_SUM:
          let total = this.sumFromOldest(article, render_type);

          return this.interval
            .map(
              d => article[render_type][d.start.toFormat(this.period_type)] || 0
            )
            .map(c => {
              total += c;
              return total;
            });
      }
    },
    renderTypeLabel(render_type) {
      if (render_type === this.RENDER_TYPE_VIEW) {
        return "PV";
      }
      if (render_type === this.RENDER_TYPE_CONVERSION) {
        return "CV";
      }
    }
  },
  computed: {
    optionTypes() {
      return this.OPTION_TYPES.map(item => {
        item.text = this.__(item.text);
        return item;
      });
    },
    optionModes() {
      return this.OPTION_MODES.map(item => {
        item.text = this.__(item.text);
        return item;
      });
    },
    optionRenderTypes() {
      return this.OPTION_RENDER_TYPES.map(type => {
        type.text = this.__(type.text);
        return type;
      });
    },
    interval_type() {
      switch (this.type) {
        case this.TYPE_DAILY:
          return { days: 1 };
        case this.TYPE_MONTHLY:
          return { months: 1 };
        case this.TYPE_YEARLY:
          return { years: 1 };
      }
    },
    label_type() {
      switch (this.type) {
        case this.TYPE_DAILY:
          return this.DISPLAY_FORMAT_DAILY;
        case this.TYPE_MONTHLY:
          return this.DISPLAY_FORMAT_MONTHLY;
        case this.TYPE_YEARLY:
          return this.DISPLAY_FORMAT_YEARLY;
      }
    },
    period_type() {
      switch (this.type) {
        case this.TYPE_DAILY:
          return this.FORMAT_DAILY;
        case this.TYPE_MONTHLY:
          return this.FORMAT_MONTHLY;
        case this.TYPE_YEARLY:
          return this.FORMAT_YEARLY;
      }
    },
    interval() {
      return Interval.fromDateTimes(
        DateTime.fromISO(this.start),
        DateTime.fromISO(this.end)
      ).splitBy(this.interval_type);
    },
    labels() {
      return this.interval.map(d => d.start.toFormat(this.label_type));
    },
    datasets() {
      return this.render_types
        .map(r => {
          return this.articles
            .filter(a => a.checked)
            .map(a => {
              return {
                type: "line",
                label: `${a.title}(${this.renderTypeLabel(r)})`,
                backgroundColor: this.getColor(a, r, ".5"),
                borderColor: this.getColor(a, r),
                borderWidth: 2,
                pointRadius: 1,
                fill: false,
                data: this.calcData(a, r)
              };
            });
        })
        .flat();
    }
  }
};
</script>
<style lang="scss" scoped>
@import "../../sass/mypage/variables";

h5 {
  margin: 1rem 0;
}
section {
  margin-left: 2rem;
  margin-bottom: 1rem;
}
span {
  margin-right: 1rem;
}
.checked {
  background-color: rgba($primary, 0.3);
}
</style>
