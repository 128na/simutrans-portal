/**
 *  設定用定数
 *  <template>内で利用するにはmixinする必要がある
 * */
const values = {
    TYPE_DAILY: 1,
    TYPE_MONTHLY: 2,
    TYPE_YEARLY: 3,

    MODE_LINE: 'line',
    MODE_SUM: 'sum',

    RENDER_TYPE_VIEW: 'view_counts',
    RENDER_TYPE_CONVERSION: 'conversion_counts',

    FORMAT_DAILY: 'yyyyLLdd',
    FORMAT_MONTHLY: 'yyyyLL',
    FORMAT_YEARLY: 'yyyy',

    DISPLAY_FORMAT_DAILY: 'yyyy/LL/dd',
    DISPLAY_FORMAT_MONTHLY: 'yyyy/LL',
    DISPLAY_FORMAT_YEARLY: 'yyyy',
};

values.OPTION_TYPES = [
    { text: '日間', value: values.TYPE_DAILY },
    { text: '月間', value: values.TYPE_MONTHLY },
    { text: '年間', value: values.TYPE_YEARLY },
];
values.OPTION_MODES = [
    { text: '推移', value: values.MODE_LINE },
    { text: '合計', value: values.MODE_SUM },
];
values.OPTION_RENDER_TYPES = [
    { text: 'PV', value: values.RENDER_TYPE_VIEW },
    { text: 'CV', value: values.RENDER_TYPE_CONVERSION },
];

export default {
    data() {
        return {
            ...values,
        }
    }
}
