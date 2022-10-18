import { DateTime } from 'luxon';

export const DT_FORMAT = 'yyyy/LL/dd HH:mm';
export const D_FORMAT = 'yyyy/LL/dd';
export const defaultDateTime = () => DateTime.now().plus({ hours: 1 });
export const DEFAULT_THUMBNAIL = '/storage/default/image.png';
