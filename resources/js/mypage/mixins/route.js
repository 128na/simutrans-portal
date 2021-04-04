export default {
  methods: {
    goto(route) {
      this.$router.push(route).catch((e) => { });
    },
    route_edit_article(id) {
      return { name: "editArticle", params: { id } };
    },
    scrollToTop() {
      window.scrollTo({ top: 0, behavior: "smooth" });
    }
  },
  computed: {
    base_url() {
      return process.env.NODE_ENV === "production"
        ? process.env.MIX_APP_URL
        : "/";
    },
    top_url() {
      return `${this.base_url}`;
    },
    admin_url() {
      return `${this.base_url}/admin`;
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
  }
}
