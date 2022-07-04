export const defaultArticle = {
  methods: {
    createDefaultArticle(postType) {
      switch (postType) {
        case 'addon-post':
          return this.createAddonPost();
        case 'addon-introduction':
          return this.createAddonIntroduction();
        case 'page':
          return this.createPage();
        case 'markdown':
          return this.createMarkdown();
      }
    },
    createAddonPost() {
      return {
        post_type: 'addon-post',
        title: '',
        slug: '',
        status: 'draft',
        contents: {
          thumbnail: null,
          author: '',
          description: '',
          file: null,
          license: '',
          thanks: ''
        },
        categories: [],
        tags: [],
        published_at: null
      };
    },
    createAddonIntroduction() {
      return {
        post_type: 'addon-introduction',
        title: '',
        slug: '',
        status: 'draft',
        contents: {
          thumbnail: null,
          agreement: false,
          exclude_link_check: false,
          author: '',
          description: '',
          license: '',
          link: '',
          thanks: ''
        },
        categories: [],
        tags: [],
        published_at: null
      };
    },
    createPage() {
      return {
        post_type: 'page',
        title: '',
        slug: '',
        status: 'draft',
        contents: {
          thumbnail: null,
          sections: [{ type: 'text', text: '' }]
        },
        categories: [],
        published_at: null
      };
    },
    createMarkdown() {
      return {
        post_type: 'markdown',
        title: '',
        slug: '',
        status: 'draft',
        contents: {
          thumbnail: null,
          markdown: ''
        },
        categories: [],
        published_at: null
      };
    }
  }
};
