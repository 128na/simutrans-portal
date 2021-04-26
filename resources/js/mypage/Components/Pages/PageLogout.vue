<template></template>
<script>
import { validateLogin } from "../../mixins/auth";
import { mapGetters, mapActions } from "vuex";
export default {
  mixins: [validateLogin],
  created() {
    if (this.isLoggedIn) {
      this.logout();
      this.clearArticles();
      this.clearAnalytics();
      this.clearAttachments();
      this.clearBookmarks();
      this.clearOptions();
      this.clearTags();
    }
  },
  computed: {
    ...mapGetters(["isLoggedIn"]),
  },
  methods: {
    ...mapActions([
      "logout",
      "clearArticles",
      "clearAnalytics",
      "clearAttachments",
      "clearBookmarks",
      "clearOptions",
      "clearTags",
    ]),
  },
};
</script>
