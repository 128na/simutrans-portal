/**
 * 分析用の定数
 */
export const analytics_constants = {
  computed: {
    TYPE_DAILY: () => "daily",
    TYPE_MONTHLY: () => "monthly",
    TYPE_YEARLY: () => "yearly",

    MODE_LINE: () => 'line',
    MODE_SUM: () => 'sum',

    AXIS_VIEW: () => 'pv',
    AXIS_CONVERSION: () => 'cv',

    INDEX_OF_ARCHIVE_ID: () => 0,
    INDEX_OF_VIEW: () => 1,
    INDEX_OF_CONVERSION: () => 2,
    OPTIONS() {
      return {
        types: [
          { value: "daily", text: this.$t("Daily") },
          { value: "monthly", text: this.$t("Monthly") },
          { value: "yearly", text: this.$t("Yearly") }
        ],
        modes: [
          { value: "line", text: this.$t("Transition") },
          { value: "sum", text: this.$t("Total") }
        ],
        axes: [
          { value: "pv", text: this.$t("Page Views") },
          { value: "cv", text: this.$t("Conversions") }
        ]
      }
    }
  }
};
