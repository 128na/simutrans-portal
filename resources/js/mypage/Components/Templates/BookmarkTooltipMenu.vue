<template>
  <b-card body-class="p-1 shadow">
    <template slot="header">
      {{ item.title }}
    </template>
    <b-nav vertical>
      <b-nav-item
        v-if="item.is_public"
        class="mb-1"
        @click="handleCopy(public_url)"
      >
        <b-icon icon="clipboard-data" class="mr-1" />
        公開用URLをコピー
      </b-nav-item>
      <b-nav-item
        v-if="item.is_public"
        :href="public_url"
        target="_blank"
        class="mb-1"
      >
        <b-icon icon="box-arrow-up-right" class="mr-1" />
        表示
      </b-nav-item>
      <b-nav-item @click="handleEdit">
        <b-icon icon="pencil" class="mr-1" />
        編集
      </b-nav-item>
      <b-nav-item v-show="item.is_public" @click="handleToPrivate">
        <b-icon icon="lock-fill" class="mr-1" />
        非公開にする
      </b-nav-item>
      <b-nav-item v-show="!item.is_public" @click="handleToPublish">
        <b-icon icon="unlock-fill" class="mr-1" />
        公開にする
      </b-nav-item>
      <b-nav-item @click="handleDelete">
        <b-icon icon="trash" class="mr-1" />
        削除
      </b-nav-item>
    </b-nav>
  </b-card>
</template>
<script>
import { mapActions } from "vuex";
export default {
  props: ["item"],
  computed: {
    public_url() {
      return `${this.base_url}/public-bookmarks/${this.item.uuid}`;
    },
  },
  methods: {
    ...mapActions(["updateBookmark", "deleteBookmark", "setInfoMessage"]),
    handleEdit() {
      this.goto(this.route_edit_item(this.item.id));
    },
    handleToPrivate() {
      const params = {
        bookmark: Object.assign({}, this.item, {
          is_public: false,
        }),
        bookmarkItems: this.item.bookmarkItems,
      };
      this.updateBookmark({
        params,
        message: "ステータスを非公開にしました",
      });
      this.$emit("close");
    },
    handleToPublish() {
      const params = {
        bookmark: Object.assign({}, this.item, {
          is_public: true,
        }),
        bookmarkItems: this.item.bookmarkItems,
      };
      this.updateBookmark({
        params,
        message: "ステータスを公開にしました",
      });
      this.$emit("close");
    },
    handleCopy(text) {
      this.$copyText(text);
      this.setInfoMessage({
        message: "クリップボードにコピーしました",
      });
      this.$emit("close");
    },
    handleDelete() {
      if (confirm("ブックマークを削除しますか？")) {
        this.deleteBookmark({
          bookmark_id: this.item.id,
        });
        this.$emit("close");
      }
    },
  },
};
</script>
