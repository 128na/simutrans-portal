<template>
  <div>
    <header-menu :user="user" @logout="handleLogout" />
    <main class="container-fluid bg-light py-4">
      <div class="alert alert-danger" v-show="errorMessage">
        [{{ statusCode }}] {{ errorMessage }}
      </div>
      <transition name="fade" mode="out-in">
        <router-view />
      </transition>
    </main>
  </div>
</template>
<script>
import { DateTime } from "luxon";
import { mapGetters, mapActions } from "vuex";
export default {
  data() {
    return {
      initialized: true,
    };
  },
  created() {
    this.checkLogin();
  },
  methods: {
    ...mapActions(["checkLogin", "logout"]),
    handleLogout() {
      this.logout();
    },
    async handleLoggedin(user) {
      this.user = user;

      await Promise.all([
        this.fetchAttachments(),
        this.fetchArticles(),
        this.fetchOptions(),
      ]);
      this.$router.push({ name: "index" });
      this.toastSuccess("Logged in");
    },

    async setUser(user) {
      this.user = user;

      await Promise.all([
        this.fetchAttachments(),
        this.fetchArticles(),
        this.fetchOptions(),
      ]);
    },
    setAttachments(attachments) {
      this.attachments = attachments;
    },
    setArticles(articles) {
      this.articles = articles.map((a) =>
        Object.assign(a, {
          created_at: DateTime.fromISO(a.created_at),
          updated_at: DateTime.fromISO(a.updated_at),
        })
      );
    },
    setOptions(options) {
      this.options = options;
    },
    handleUser(user) {
      this.toastSuccess("Profile Updated.");
      this.setUser(user);
    },
    handleAttachments(attachments) {
      this.setAttachments(attachments);
    },
    handleArticles(articles) {
      this.toastSuccess("Article Updated.");
      this.setArticles(articles);
    },
  },
  computed: {
    ...mapGetters(["errorMessage", "statusCode", "user"]),
  },
};
</script>
<style>
.pre-line {
  white-space: pre-line;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.1s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}
/* 汎用カーソル */
.clickable {
  cursor: pointer;
}
:disabled {
  cursor: not-allowed;
}
/* アイコンの縦位置調整 */
a.dropdown-item,
a.btn,
button.btn {
  display: inline-flex;
  align-items: center;
}
</style>
