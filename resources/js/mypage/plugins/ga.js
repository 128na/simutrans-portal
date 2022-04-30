const hasGtag = typeof gtag === 'function';
const baseUrl = process.env.NODE_ENV === 'production' ? '/portal' : '';

export function gaPageView(to, prefix = '/mypage') {
  if (hasGtag) {
    // eslint-disable-next-line no-undef
    gtag('event', 'page_view', {
      page_path: `${baseUrl}${prefix}${to.path}`
    });
  }
}
