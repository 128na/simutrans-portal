export const mockArticlesResponse = {
  statusCode: 200,
  body: {
    data: [{
      id: 1,
      title: 'dummy title',
      slug: 'dummy-slug',
      status: 'publish',
      post_type: 'addon-introduction',
      contents: {
        author: null,
        link: 'https://128na.github.io/iss',
        description: null,
        license: null,
        thanks: null,
        thumbnail: null,
      },
      categories: [],
      tags: [],
      created_at: '2022-01-02T03:04:05',
      published_at: '2022-02-03T04:05:06',
      modified_at: '2022-03-04T05:06:07',
      url: 'http://localhost:8080/articles/dummy-slug',
      metrics: {
        totalViewCount: 1,
        totalConversionCount: 2,
        totalRetweetCount: 3,
        totalReplyCount: 4,
        totalLikeCount: 5,
        totalQuoteCount: 6,
        totalImpressionCount: 7,
        totalUrlLinkClicks: 8,
        totalUserProfileClicks: 9,
      },
    }],
  },
};
export const mockOptionsResponse = {
  statusCode: 200,
  body: {
    categories: {
      addon: [{
        id: 1, name: 'dummy1', type: 'addon', slug: 'dummy1',
      }],
      license: [{
        id: 2, name: 'dummy2', type: 'addon', slug: 'dummy2',
      }],
      pak: [{
        id: 3, name: 'dummy3', type: 'addon', slug: 'dummy3',
      }],
      pak128_position: [{
        id: 4, name: 'dummy4', type: 'addon', slug: 'dummy4',
      }],
      page: [{
        id: 5, name: 'dummy5', type: 'addon', slug: 'dummy5',
      }],
    },
    statuses: [
      { label: '公開', value: 'publish' },
      { label: '予約投稿', value: 'reservation' },
      { label: '下書き', value: 'draft' },
      { label: '非公開', value: 'private' },
      { label: 'ゴミ箱', value: 'trash' },
    ],
  },
};
export const mockAttachmentsResponse = {
  statusCode: 200,
  body: {
    data: [],
  },
};

export const mockTagsResponse = {
  statusCode: 200,
  body: {
    data: [],
  },
};
