import { Line, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins

const FONT_COLOR = "rgba(0, 0, 0, 1)";
const GRID_LINES_SETTING = {
    display: true,
    drawOnChartArea: false,
    color: "rgba(0, 0, 0, .5)",
    zeroLineColor: "rgba(0, 0, 0, 1)"
};

const OPTIONS = {
    responsive: true,
    maintainAspectRatio: false,
    legend: {
        // display: false
        onClick(event, legendItem) {
            return;
        },
        fullWidth: false,
        labels: {
            boxWidth: 20,
            fontColor: FONT_COLOR
        },
    },
    layout: {
        padding: {
            top: 20,
            left: 20,
            bottom: 20,
            right: 20
        }
    },
    scales: {
        xAxes: [
            {
                gridLines: GRID_LINES_SETTING,
                ticks: {
                    autoSkip: true,
                    fontColor: FONT_COLOR,
                    fontSize: 14
                },
                scaleLabel: {
                    display: true,
                    fontColor: FONT_COLOR,
                    labelString: "日付",
                },
            },
        ],
        yAxes: [
            {
                id: "view_counts",
                type: "linear",
                gridLines: GRID_LINES_SETTING,
                scaleLabel: {
                    display: true,
                    fontColor: FONT_COLOR,
                    labelString: "閲覧数"
                },
                ticks: {
                    autoSkip: true,
                    fontColor: FONT_COLOR,
                    fontSize: 14,
                    min: 0,
                },
            },
            {
                id: "conversion_counts",
                type: "linear",
                gridLines: GRID_LINES_SETTING,
                scaleLabel: {
                    display: true,
                    fontColor: FONT_COLOR,
                    labelString: "コンバージョン（遷移・DL数）"
                },
                ticks: {
                    autoSkip: true,
                    fontColor: FONT_COLOR,
                    fontSize: 14,
                    min: 0,
                },
                position: "right"
            }
        ],
    }
};
export default {
    extends: Line,
    mixins: [reactiveProp],
    mounted() {
        // this.chartData is created in the mixin.
        // If you want to pass options please create a local options object
        this.renderChart(this.chartData, OPTIONS)
    }
}
