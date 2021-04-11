<template>
  <div>
    <page-title>ブックマーク一覧</page-title>
    <page-description>作成したブックマークの管理ができます</page-description>
    <div v-if="ready">
      <div v-if="!isVerified">
        <need-verify />
      </div>
      <div v-else>
        <div class="mb-4">
          <b-button variant="primary" :to="route_edit_bookmark()">
            新規作成
          </b-button>
        </div>

        <div>
          <bookmark-table :bookmarks="bookmarks" />
        </div>
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
    if (this.isLoggedIn && !this.bookmarksLoaded) {
      this.fetchBookmarks();
    }
  },
  computed: {
    ...mapGetters([
      "isLoggedIn",
      "initialized",
      "isVerified",
      "bookmarksLoaded",
      "bookmarks",
    ]),
    ready() {
      return this.bookmarksLoaded;
    },
  },
  methods: {
    ...mapActions(["fetchBookmarks"]),
  },
};
</script>
