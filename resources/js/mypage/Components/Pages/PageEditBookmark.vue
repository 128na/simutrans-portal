<template>
  <div>
    <page-title>編集</page-title>
    <div v-if="ready">
      <form-bookmark :bookmark="copy">
        <fetching-overlay>
          <b-button variant="primary" @click.prevent="handleSave">
            保存
          </b-button>
        </fetching-overlay>
      </form-bookmark>
    </div>
    <loading v-else />
  </div>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
import { validateVerified } from "../../mixins/auth";
import { editor } from "../../mixins/editor";
export default {
  mixins: [validateVerified, editor],
  created() {
    if (this.isVerified) {
      if (!this.bookmarksLoaded) {
        this.fetchBookmarks();
      } else {
        this.setCopy(this.selected_bookmark);
      }
    }
  },
  watch: {
    bookmarksLoaded(val) {
      if (val) {
        this.setCopy(this.selected_bookmark);
      }
    },
  },
  computed: {
    is_create() {
      return !this.copy.id;
    },
    ...mapGetters([
      "isVerified",
      "getStatusText",
      "bookmarksLoaded",
      "bookmarks",
      "hasError",
    ]),
    selected_bookmark() {
      if (this.bookmarksLoaded) {
        return (
          this.bookmarks.find((b) => b.id == this.$route.params.id) ||
          this.createBookmark()
        );
      }
    },
    ready() {
      return this.bookmarksLoaded && !!this.copy;
    },
  },
  methods: {
    createBookmark() {
      return {
        title: "",
        description: "",
        is_public: false,
        bookmarkItems: [],
      };
    },
    ...mapActions(["fetchBookmarks", "updateBookmark", "storeBookmark"]),
    async handlePreview() {
      const params = {
        bookmark: this.copy,
        bookmarkItems: this.copy.bookmarkItems,
      };

      this.scrollToTop();
    },
    async handleSave() {
      const params = {
        bookmark: this.copy,
        bookmarkItems: this.copy.bookmarkItems,
      };
      if (this.is_create) {
        await this.storeBookmark({ params });
      } else {
        await this.updateBookmark({ params });
      }

      // 更新が成功すれば遷移ダイアログを無効化してブックマーク一覧へ戻る
      // エラーがあれば編集画面上部へスクロールする（通知が見えないため）
      if (!this.hasError) {
        this.unsetUnloadDialog();
        this.$router.push({ name: "bookmarks" });
      } else {
        this.scrollToTop();
      }
    },
    getOriginal() {
      return this.selected_bookmark;
    },
  },
};
</script>
