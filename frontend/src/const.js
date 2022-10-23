import { DateTime } from 'luxon';

export const DT_FORMAT = 'yyyy/LL/dd HH:mm';
export const D_FORMAT = 'yyyy/LL/dd';
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
