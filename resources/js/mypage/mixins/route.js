export default {
  methods: {
    goto(route) {
      this.$router.push(route).catch((e) => { });
    },
    route_edit_article(id) {
      return { name: "editArticle", params: { id } };
    },
    route_edit_bookmark(id = null) {
      return { name: "editBookmark", params: { id } };
    },
    scrollToTop() {
      window.scrollTo({ top: 0, behavior: "smooth" });
    }
  },
  computed: {
    base_url() {
      return process.env.MIX_APP_URL;
    },
    top_url() {
      return `${this.base_url}`;
    },
    admin_url() {
      return `${this.base_url}/admin`;
    },
    can_register() {
      return process.env.MIX_REGISTER_RESTRICTION === 'false';
    },
    can_twitter_login() {
      return process.env.MIX_TWITTER_LOGIN_RESTRICTION === 'false';
    },
    can_google_login() {
      return process.env.MIX_GOOGLE_LOGIN_RESTRICTION === 'false';
    },
    twitter_login_url() {
      return `${this.base_url}/login/twitter`;
    },
    google_login_url() {
      return `${this.base_url}/login/google`;
    },

    route_login() {
      return { name: "login" };
    },
    route_logout() {
      return { name: "logout" };
    },
    route_register() {
      return { name: "register" };
    },
    route_password_reset() {
      return { name: "reset" };
    },
    route_mypage_index() {
      return { name: "index" };
    },
    route_analytics() {
      return { name: "analyticsArticle" };
    },
    route_create_addon_post() {
      return { name: "createArticle", params: { post_type: "addon-post" } };
    },
    route_create_addon_introduction() {
      return { name: "createArticle", params: { post_type: "addon-introduction" } };
    },
    route_create_page() {
      return { name: "createArticle", params: { post_type: "page" } };
    },
    route_create_markdown() {
      return { name: "createArticle", params: { post_type: "markdown" } };
    },
    route_edit_profile() {
      return { name: "editProfile" };
    },
    route_bookmarks() {
      return { name: "bookmarks" };
    },
  }
}
