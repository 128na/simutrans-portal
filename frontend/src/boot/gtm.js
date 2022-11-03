/* eslint-disable no-console */
export default {
  frontConversionDownload() {
    if (window.dataLayer) {
      window.dataLayer.push({ event: 'front:conversion:download' });
    } else {
      console.warn('missing dataLayer');
    }
  },
  frontConversionLink() {
    if (window.dataLayer) {
      window.dataLayer.push({ event: 'front:conversion:link' });
    } else {
      console.warn('missing dataLayer');
    }
  },
  resetDataLayer(id) {
    if (window.google_tag_manager && window.google_tag_manager[id]) {
      window.google_tag_manager[id].dataLayer.reset();
    } else {
      // console.warn('missing gtm');
    }
    if (window.dataLayer) {
      window.dataLayer.push({ event: 'common:pageview' });
    } else {
      console.warn('missing dataLayer');
    }
  },
};
