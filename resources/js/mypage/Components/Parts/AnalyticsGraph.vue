<template>
  <div :style="style">
    <canvas ref="chart" :style="style" />
  </div>
</template>
<script>
import Chart from "chart.js";
export default {
  name: "analytics-graph",
  data() {
    return {
      chart: null
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
          scales: {
            xAxes: [
              {
                ticks: {
                  autoSkip: true
                },
                scaleLabel: {
                  display: true
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
  computed: {
    style() {
      return { height: `${this.height}px` };
    }
  },
  mounted() {
    this.render();
  },
  methods: {
    test() {
      var ctx = this.$refs.chart.getContext("2d");
      new Chart(ctx, {
        // The type of chart we want to create
        type: "line",

        // The data for our dataset
        data: {
          labels: this.labels,
          datasets: [
            {
              label: "My First dataset",
              backgroundColor: "rgb(255, 99, 132)",
              borderColor: "rgb(255, 99, 132)",
              data: [0, 10, 5, 2, 20, 30, 45]
            }
          ]
        },

        // Configuration options go here
        options: this.options
      });
    },
    render() {
      const ctx = this.$refs.chart.getContext("2d");
      const chart = new Chart(ctx, {
        type: "line",
        data: {
          labels: JSON.parse(JSON.stringify(this.labels)),
          datasets: JSON.parse(JSON.stringify(this.datasets))
        },
        options: this.options
      });
      console.log(chart);
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
