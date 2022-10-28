import { DateTime } from 'luxon';

export const DT_FORMAT = 'yyyy/LL/dd HH:mm';
export const D_FORMAT = 'yyyy/LL/dd';
export const M_FORMAT = 'yyyy/LL';
export const Y_FORMAT = 'yyyy';
export const defaultDateTime = () => DateTime.now().plus({ hours: 1 });
export const DEFAULT_THUMBNAIL = '/storage/default/image.png';

export const POST_TYPE_ADDON_INTRODUCTION = 'addon-introduction';
export const POST_TYPE_ADDON_POST = 'addon-post';
export const POST_TYPE_PAGE = 'page';
export const POST_TYPE_MARKDOWN = 'markdown';

export const POST_TYPES = {
  [POST_TYPE_ADDON_INTRODUCTION]: 'アドオン投稿',
  [POST_TYPE_ADDON_POST]: 'アドオン紹介',
  [POST_TYPE_PAGE]: '一般記事',
  [POST_TYPE_MARKDOWN]: '一般記事(markdown)',
};
export const STATUSES = {
  trashed: 'ゴミ箱',
  private: '非公開',
  draft: '下書き',
  reservation: '予約投稿',
  publish: '公開',
};

export const ANALYTICS_TYPE_DAILY = 'daily';
export const ANALYTICS_TYPE_MONTHLY = 'monthly';
export const ANALYTICS_TYPE_YEARLY = 'yearly';
export const ANALYTICS_TYPES = [
  { value: ANALYTICS_TYPE_DAILY, label: '日次' },
  { value: ANALYTICS_TYPE_MONTHLY, label: '月次' },
  { value: ANALYTICS_TYPE_YEARLY, label: '年次' },
];

export const ANALYTICS_MODE_LINE = 'line';
export const ANALYTICS_MODE_SUM = 'sum';
export const ANALYTICS_MODES = [
  { value: ANALYTICS_MODE_LINE, label: '推移' },
  { value: ANALYTICS_MODE_SUM, label: '合計' },
];

export const ANALYTICS_AXIS_PV = 'pv';
export const ANALYTICS_AXIS_CV = 'cv';
export const ANALYTICS_AXIS_DATA_INDEXES = { [ANALYTICS_AXIS_PV]: 1, [ANALYTICS_AXIS_CV]: 2 };
export const ANALYTICS_AXES = [
  { value: ANALYTICS_AXIS_PV, label: 'PV（ページ表示回数）' },
  { value: ANALYTICS_AXIS_CV, label: 'CV（DL・リンククリック回数）' },
];
