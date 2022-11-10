export const createMockArticleData = (params = {}) => ({
  title: 'ダミー記事タイトル',
  status: 'publish',
  slug: 'dummy-slug',
  post_type: 'addon-introduction',
  contents: {
    agreement: true,
    author: 'ダミーユーザー',
    description: 'dummy description',
    license: 'dummy license',
    link: 'http://example.com',
    thanks: 'dummy thanks',
    thumbnail: null,
  },
  tags: [],
  categories: [],
  attachments: [],
  user: { id: 1, name: 'dummy user' },
  published_at: '2022-01-02T03:04:05.000000Z',
  modified_at: '2022-01-02T03:04:05.000000Z',
  ...params,
});

export const mockSidebarResponse = {
  statusCode: 200,
  body: {
    userAddonCounts: [{ user_id: 1, name: 'dummy user', count: 72 }],
    pakAddonCounts: {
      DummyPak: [{
        pak_slug: 'dummy-pak', addon_slug: 'dummy-addon', pak: 'Dummy Pak', addon: 'Dummy Addon', count: 334,
      }],
    },
  },
};
