const has_gtag = typeof gtag === 'function';
const base_url = process.env.NODE_ENV === 'production' ? '/portal' : '';

export function ga_page_view(to, prefix = '/mypage') {
  if (has_gtag) {
    gtag('event', 'page_view', {
      page_path: `${base_url}${prefix}${to.path}`,
    });
  }
}
