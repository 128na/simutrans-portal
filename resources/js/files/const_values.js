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
    { text: 'Daily', value: values.TYPE_DAILY },
    { text: 'Monthly', value: values.TYPE_MONTHLY },
    { text: 'Yearly', value: values.TYPE_YEARLY },
];
values.OPTION_MODES = [
    { text: 'Transition', value: values.MODE_LINE },
    { text: 'Total', value: values.MODE_SUM },
];
values.OPTION_RENDER_TYPES = [
    { text: 'Page Views', value: values.RENDER_TYPE_VIEW },
    { text: 'Conversions', value: values.RENDER_TYPE_CONVERSION },
];

export default {
    data() {
        return {
            ...values,
        }
    }
}
