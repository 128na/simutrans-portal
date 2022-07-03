export const TARGET_TYPE_USER = 'user';
export const RETRY_INTERVAL = 5000;
export const RETRY_LIMIT = 60;

export const GA_EVENTS = {
  LINK_CLICK: 'portal_link_click',
  DOWNLOAD_CLICK: 'portal_download_click'
};

export const ARTICLE_FIELDS = [
  {
    key: 'id',
    label: 'ID',
    desc: '記事の自動採番ID',
    sortable: true
  },
  {
    key: 'status',
    label: 'ステータス',
    desc: '記事の公開状態',
    sortable: true
  },
  {
    key: 'post_type',
    label: '形式',
    desc: '記事の形式',
    sortable: true
  },
  {
    key: 'title',
    label: 'タイトル',
    desc: '記事のタイトル',
    sortable: true
  },
  {
    key: 'totalViewCount',
    label: 'PV',
    desc: '記事の表示回数',
    sortable: true
  },
  {
    key: 'totalConversionCount',
    label: 'CV',
    desc: 'アドオンのダウンロード、掲載URLのクリック回数',
    sortable: true
  },
  {
    key: 'totalRetweetCount',
    label: 'RT',
    desc: '自動ツイートの合計RT数',
    sortable: true
  },
  {
    key: 'totalReplyCount',
    label: 'Rep',
    desc: '自動ツイートの合計返信数',
    sortable: true
  },
  {
    key: 'totalLikeCount',
    label: 'Like',
    desc: '自動ツイートの合計いいね数',
    sortable: true
  },
  {
    key: 'totalQuoteCount',
    label: 'QRT',
    desc: '自動ツイートの合計引用リツーイト数',
    sortable: true
  },
  {
    key: 'totalImpressionCount',
    label: 'IC',
    desc: '自動ツイートの合計表示回数',
    sortable: true
  },
  {
    key: 'totalUrlLinkClicks',
    label: 'LC',
    desc: '自動ツイートのURL合計クリック数',
    sortable: true
  },
  {
    key: 'totalUserProfileClicks',
    label: 'UC',
    desc: '自動ツイートのプロフィール合計クリック数',
    sortable: true
  },
  {
    key: 'published_at',
    label: '投稿日時',
    desc: '記事の投稿（予約）日時',
    sortable: true
  },
  {
    key: 'modified_at',
    label: '最終更新日時',
    desc: '記事の最終更新日時',
    sortable: true
  }
];
