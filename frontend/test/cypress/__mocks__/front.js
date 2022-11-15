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

export const mockArtcile = (params = {}) => ({
  title: 'ダミー記事タイトル',
  status: 'publish',
  slug: 'dummy-slug',
  post_type: 'addon-introduction',
  contents: { thumbnail: null },
  tags: [],
  categories: [],
  attachments: [],
  user: { id: 1, name: 'dummy user' },
  published_at: '2022-01-02T03:04:05.000000Z',
  modified_at: '2022-01-02T03:04:05.000000Z',
  ...params,
});

export const mockAddonPost = () => mockArtcile({
  post_type: 'addon-post',
  contents: {
    author: 'ダミーユーザー',
    description: 'dummy description',
    license: 'dummy license',
    file: 1,
    thanks: 'dummy thanks',
    thumbnail: null,
  },
  attachments: [{ id: 1, url: 'http://example.com' }],
});

export const mockAddonIntroduction = () => mockArtcile({
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
});

export const mockPage = () => mockArtcile({
  post_type: 'page',
  contents: {
    sections: [
      { type: 'caption', caption: 'dummy caption' },
      { type: 'text', text: 'dummy text' },
      { type: 'url', url: 'http://example.com' },
      { type: 'image', id: 1 },
    ],
    thumbnail: null,
  },
  attachments: [{ id: 1, url: 'https://placehold.jp/150x150.png' }],
});

export const mockMarkdown = () => mockArtcile({
  post_type: 'markdown',
  contents: {
    markdown: '# dummy markdown',
    thumbnail: null,
  },
});
