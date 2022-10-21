import { DateTime } from 'luxon';

export const DT_FORMAT = 'yyyy/LL/dd HH:mm';
export const D_FORMAT = 'yyyy/LL/dd';
export const defaultDateTime = () => DateTime.now().plus({ hours: 1 });
export const DEFAULT_THUMBNAIL = '/storage/default/image.png';

export const POST_TYPE_ADDON_INTRODUCTION = 'addon-introduction';
export const POST_TYPE_ADDON_POST = 'addon-post';
export const POST_TYPE_PAGE = 'page';
export const POST_TYPE_MARKDOWN = 'markdown';
