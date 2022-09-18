export const routeLink = {
  methods: {
    toCategory(category) {
      return {
        name: 'category',
        params: { type: category.type, slug: category.slug }
      };
    },
    toCategoryPakByAddon(addon) {
      return {
        name: 'categoryPak',
        params: { size: addon.pak_slug, slug: addon.addon_slug }
      };
    },
    toTag(tag) {
      return {
        name: 'tag',
        params: { id: tag.id }
      };
    },
    toUser(user) {
      return {
        name: 'user',
        params: { id: user.id }
      };
    },
    toUserByAddon(addon) {
      return {
        name: 'user',
        params: { id: addon.user_id }
      };
    },
    toArticle(article) {
      return {
        name: 'show',
        params: { slug: article.slug }
      };
    },
    toArticleBySlug(slug) {
      return {
        name: 'show',
        params: { slug }
      };
    }
  },
  computed: {
    toTags() {
      return {
        name: 'tags'
      };
    },
    toAdvancedSearch() {
      return {
        name: 'advancedSearch'
      };
    },
    toAbout() {
      return this.toArticleBySlug('about');
    },
    toPrivacy() {
      return this.toArticleBySlug('privacy');
    }
  }
};

export const appInfo = {
  computed: {
    appName() {
      return process.env.MIX_APP_NAME;
    },
    appVersion() {
      return process.env.MIX_APP_VERSION;
    },
    appUrl() {
      return process.env.MIX_APP_URL;
    }
  }
};

export const watchAndFetch = {
  // https://router.vuejs.org/guide/advanced/data-fetching.html#fetching-after-navigation
  created() {
    if (!this.fetch) {
      throw new Error('[watchAndFetch mixin] fetch method not defined');
    }
    // watch the params of the route to fetch the data again
    this.$watch(
      () => this.$route.params,
      () => {
        this.fetch();
      },
      // fetch the data when the view is created and the data is
      // already being observed
      { immediate: true }
    );
  },
  methods: {
    handleError(err) {
      this.$router.push({ name: 'error', params: { status: err?.response?.status || 0 } });
    }
  }
};
