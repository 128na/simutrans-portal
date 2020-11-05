<template>
  <div>
    <page-title>マイページ</page-title>
    <page-description>投稿した記事の管理ができます</page-description>
    <div v-if="ready">
      <div v-if="!isVerified">
        <need-verify />
      </div>
      <div v-else>
        <article-table :articles="articles" :user="user" />
      </div>
    </div>
    <loading v-else />
  </div>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import { validateLogin } from "../../mixins/auth";
export default {
  mixins: [validateLogin],
  created() {
    if (this.isLoggedIn && !this.articlesLoaded) {
      this.fetchArticles();
    }
  },
  computed: {
    ...mapGetters([
      "isLoggedIn",
      "initialized",
      "isVerified",
      "user",
      "articlesLoaded",
      "articles",
    ]),
    ready() {
      return this.articlesLoaded;
    },
  },
  methods: {
    ...mapActions(["fetchArticles"]),
  },
};
</script>
