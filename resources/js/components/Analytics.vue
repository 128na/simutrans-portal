<template>
  <div class="analytics">
    <div class="chart-area">
      <line-chart :datasets="datasets" :labels="labels"></line-chart>
    </div>

    <div>
      <span class="clickable" @click="setType(3)">
        <input type="radio" :checked="isType(3)" />年間
      </span>
      <span class="clickable" @click="setType(2)">
        <input type="radio" :checked="isType(2)" />月間
      </span>
      <span class="clickable" @click="setType(1)">
        <input type="radio" :checked="isType(1)" />日間
      </span>
    </div>

    <div>
      <span class="clickable" @click="setMode('line')">
        <input type="radio" :checked="isMode('line')" />推移
      </span>
      <span class="clickable" @click="setMode('accumulation')">
        <input type="radio" :checked="isMode('accumulation')" />積算
      </span>
    </div>
    <div>
      <input type="text" v-model="begin" />～
      <input type="text" v-model="end" />
    </div>

    <table>
      <thead>
        <th>Title</th>
      </thead>
      <tbody>
        <tr v-for="article in articles" :key="article.id">
          <td @click="toggleChecked(article.id)" class="clickable">
            <input type="checkbox" :checked="article.checked" />
            {{ article.title }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import LineChart from "./LineChart";
import { DateTime } from "luxon";

export default {
  components: {
    LineChart
  },
  data() {
    return {
      articles: window.articles,
      type: 1,
      mode: "line",
      begin: null,
      end: DateTime.local().toFormat("yyyyLLdd")
    };
  },
  mounted() {
    const oldest = window.articles.slice(-1).pop();
    console.log(oldest);
    this.begin = oldest.created_at;
  },
  methods: {
    isType(type) {
      return this.type === type;
    },
    setType(type) {
      this.type = type;
    },
    isMode(mode) {
      return this.mode === mode;
    },
    setMode(mode) {
      this.mode = mode;
    },
    toggleChecked(id) {
      this.articles = this.articles.map(a =>
        a.id !== id ? a : Object.assign(a, { checked: !a.checked })
      );
    }
  },
  computed: {
    labels() {
      return [
        ...new Set(
          this.articles
            .filter(a => a.checked)
            .reduce(
              (arr, a) =>
                arr.concat(
                  a.view_counts
                    .filter(c => c.type === this.type)
                    .map(c => c.period)
                ),
              []
            )
        )
      ];
    },
    datasets() {
      return this.articles
        .filter(a => a.checked)
        .map(a => {
          return {
            type: "line",
            label: a.title,
            fill: false,
            data: a.view_counts
              .filter(c => c.type === this.type)
              .map(c => c.count)
          };
        });
    }
  }
};
</script>
