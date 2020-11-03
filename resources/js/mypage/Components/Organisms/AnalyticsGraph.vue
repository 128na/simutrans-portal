<template>
  <div :style="style">
    <canvas ref="chart" :style="style" />
  </div>
</template>
<script>
import Chart from "chart.js";
export default {
  data() {
    return {
      chart: null,
    };
  },
  props: {
    labels: {
      type: Array,
      default: () => [],
    },
    datasets: {
      type: Array,
      default: () => [],
    },
    options: {
      type: Object,
      default: () => {
        return {
          maintainAspectRatio: false,
          scales: {
            xAxes: [
              {
                ticks: {
                  autoSkip: true,
                },
                scaleLabel: {
                  display: true,
                },
              },
            ],
            yAxes: [
              {
                type: "linear",
                ticks: {
                  autoSkip: true,
                  min: 0,
                },
              },
            ],
          },
        };
      },
    },
    height: {
      type: Number,
      default: 400,
    },
  },
  computed: {
    style() {
      return { height: `${this.height}px` };
    },
  },
  mounted() {
    this.initialize();
  },
  methods: {
    initialize() {
      const ctx = this.$refs.chart.getContext("2d");
      this.chart = new Chart(ctx, {
        type: "line",
        data: {
          labels: this.labels,
          datasets: this.datasets,
        },
        options: this.options,
      });
    },
    render() {
      if (this.datasets.length) {
        this.chart.data.labels = this.labels;
        this.chart.data.datasets = this.datasets;
        this.chart.data.options = this.options;

        this.chart.update();
      }
    },
  },
  watch: {
    labels(val) {
      this.render();
    },
    datasets(val) {
      this.render();
    },
    options(val) {
      this.render();
    },
  },
};
</script>
