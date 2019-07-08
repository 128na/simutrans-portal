<template>
  <canvas id="myChart" width="400" height="400"></canvas>
</template>
<script>
import Chart from "chart.js";
export default {
  data() {
    return {
      cahrt: null
    };
  },
  props: {
    labels: {
      type: Array,
      default: () => []
    },
    datasets: {
      type: Array,
      default: () => []
    },
    options: {
      type: Object,
      default: () => {
        return {
          maintainAspectRatio: false,
          responsive: true,
          scales: {
            xAxes: [
              {
                ticks: {
                  autoSkip: true
                },
                scaleLabel: {
                  display: true,
                  labelString: "日付"
                }
              }
            ],
            yAxes: [
              {
                type: "linear",
                ticks: {
                  autoSkip: true,
                  min: 0
                }
              }
            ]
          }
        };
      }
    },
    height: {
      type: Number,
      default: 400
    }
  },
  mounted() {
    const ctx = document.getElementById("myChart");
    ctx.height = this.height;
    this.chart = new Chart(ctx, {
      type: "line",
      data: {
        labels: this.labels,
        datasets: this.datasets
      },
      options: this.options
    });
  },
  methods: {
    render() {
      this.chart.data.labels = this.labels;
      this.chart.data.datasets = this.datasets;
      this.chart.data.options = this.options;

      this.chart.update();
    }
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
    }
  }
};
</script>
