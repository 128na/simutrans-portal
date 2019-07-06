<template>
  <div class="analytics">
    <line-chart :chart-data="render_data"></line-chart>

    <div>
      <span class="clickable" @click="setType('yearly')">
        <input type="radio" :checked="isType('yearly')" />年間
      </span>
      <span class="clickable" @click="setType('monthly')">
        <input type="radio" :checked="isType('monthly')" />月間
      </span>
      <span class="clickable" @click="setType('daily')">
        <input type="radio" :checked="isType('daily')" />日間
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
      <input type="date" v-model="begin" />～
      <input type="date" v-model="end" />
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
import LineChart from "./LineChart.js";

export default {
  components: {
    LineChart
  },
  data() {
    return {
      articles: window.articles,
      type: "daily",
      mode: "line",
      begin: null,
      end: null
    };
  },
  mounted() {},
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
      return [];
    },
    datasets() {
      return [];
    },
    render_data() {
      return {
        labels: this.labels,
        datasets: this.datasets
      };
    }
  }
};
</script>

<style>
</style>
