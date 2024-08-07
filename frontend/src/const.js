import { DateTime } from 'luxon';

export const DT_FORMAT = 'yyyy/LL/dd HH:mm';
export const D_FORMAT = 'yyyy/LL/dd';
export const M_FORMAT = 'yyyy/LL';
export const Y_FORMAT = 'yyyy';
export const defaultDateTime = () => DateTime.now().plus({ hours: 1 });

export const DEFAULT_THUMBNAIL = '/storage/default/image.png';
export const DEFAULT_AVATAR = '/storage/default/avatar.png';

export const POST_TYPE_ADDON_INTRODUCTION = 'addon-introduction';
export const POST_TYPE_ADDON_POST = 'addon-post';
export const POST_TYPE_PAGE = 'page';
export const POST_TYPE_MARKDOWN = 'markdown';

export const POST_TYPES = {
  [POST_TYPE_ADDON_INTRODUCTION]: 'アドオン紹介',
  [POST_TYPE_ADDON_POST]: 'アドオン投稿',
  [POST_TYPE_PAGE]: '一般記事',
  [POST_TYPE_MARKDOWN]: '一般記事(markdown)',
};
export const STATUSES = {
  trashed: 'ゴミ箱',
  private: '非公開',
  draft: '下書き',
  reservation: '予約投稿',
  publish: '公開済み',
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

export const BULK_ZIP_RETRY_INTERVAL = 5000;
export const BULK_ZIP_RETRY_LIMIT = 60;

export const SUPPORT_SITE_URL = 'https://simutrans-intro.notion.site/Simutrans-Addon-Portal-c8d0ab13507d4fedace504eaac1c733e';
export const PRIVACY_POLICY_URL = 'https://simutrans-intro.notion.site/512f33db6dd94a1ca51d2607408caf33';

export const ARTICLE_COLUMNS = [
  {
    name: 'id',
    field: 'id',
    label: 'ID',
    sortable: true,
    align: 'center',
    desc: '記事のID',
  },
  {
    name: 'status',
    field: (row) => STATUSES[row.status],
    label: 'ステータス',
    sortable: true,
    align: 'left',
    desc: '記事の公開状態',
  },
  {
    name: 'post_type',
    field: (row) => POST_TYPES[row.post_type],
    label: '形式',
    sortable: true,
    align: 'left',
    desc: '記事の形式',
  },
  {
    name: 'title',
    field: 'title',
    label: 'タイトル',
    sortable: true,
    align: 'left',
    desc: '記事のタイトル',
  },
  {
    name: 'totalViewCount',
    field: (row) => row.metrics.totalViewCount,
    label: 'PV',
    sortable: true,
    desc: '記事の個別ページ表示回数。トップや記事一覧での表示回数は含みません。',
  },
  {
    name: 'totalConversionCount',
    field: (row) => row.metrics.totalConversionCount,
    label: 'CV',
    sortable: true,
    desc: 'アドオンのダウンロード、掲載URLのクリック回数',
  },
  {
    name: 'published_at',
    field: (row) => (row.published_at ? DateTime.fromISO(row.published_at).toLocaleString(DateTime.DATETIME_SHORT) : '-'),
    label: '投稿日時',
    sortable: true,
    align: 'left',
    desc: '記事の投稿（予約）日時',
  },
  {
    name: 'modified_at',
    field: (row) => DateTime.fromISO(row.modified_at).toLocaleString(DateTime.DATETIME_SHORT),
    label: '最終更新日時',
    sortable: true,
    align: 'left',
    desc: '記事の最終更新日時',
  },
];

export const ARTICLE_OPTIONS = ARTICLE_COLUMNS.map((c) => ({
  label: c.label,
  value: c.name,
  desc: c.desc,
}));
