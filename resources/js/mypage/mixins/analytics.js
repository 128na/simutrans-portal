/**
 * 分析用の定数
 */
export const analyticsConstants = {
  computed: {
    TYPE_DAILY: () => 'daily',
    TYPE_MONTHLY: () => 'monthly',
    TYPE_YEARLY: () => 'yearly',

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
          { value: 'daily', text: '日' },
          { value: 'monthly', text: '月' },
          { value: 'yearly', text: '年' }
        ],
        modes: [
          { value: 'line', text: '推移' },
          { value: 'sum', text: '合計' }
        ],
        axes: [
          { value: 'pv', text: 'PV（ページ表示回数）' },
          { value: 'cv', text: 'CV（DL・リンククリック回数）' }
        ]
      };
    }
  }
};
