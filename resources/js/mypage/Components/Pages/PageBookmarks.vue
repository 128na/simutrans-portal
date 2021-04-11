<template>
  <div>
    <page-title>ブックマーク一覧</page-title>
    <page-description>作成したブックマークの管理ができます</page-description>
    <div v-if="ready">
      <div v-if="!isVerified">
        <need-verify />
      </div>
      <div v-else>hello</div>
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
