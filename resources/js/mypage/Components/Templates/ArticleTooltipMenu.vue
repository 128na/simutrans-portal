<template>
  <b-card body-class="p-1 shadow">
    <template slot="header">
      {{ item.title }}
    </template>
    <b-nav vertical>
      <b-nav-item v-if="is_publish" class="mb-1" @click="handleCopy(item.url)">
        <b-icon icon="clipboard-data" class="mr-1" />
        URLをコピー
      </b-nav-item>
      <b-nav-item
        v-if="is_publish"
        :href="item.url"
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
      <b-nav-item v-show="is_publish" @click="handleToPrivate">
        <b-icon icon="lock-fill" class="mr-1" />
        記事を非公開にする
      </b-nav-item>
      <b-nav-item v-show="!is_publish" @click="handleToPublish">
        <b-icon icon="unlock-fill" class="mr-1" />
        記事を公開にする（自動ツイート無し）
      </b-nav-item>
    </b-nav>
  </b-card>
</template>
<script>
import { mapActions } from "vuex";
export default {
  props: ["item"],
  computed: {
    is_publish() {
      return this.item.status === "公開";
    },
  },
  methods: {
    ...mapActions(["updateArticle", "setInfoMessage"]),
    handleEdit() {
      this.goto(this.route_edit_article(this.item.id));
    },
    handleToPrivate() {
      const params = {
        article: Object.assign({}, this.item, {
          status: "private",
        }),
      };
      this.updateArticle({
        params,
        message: "ステータスを非公開にしました",
      });
      this.$emit("close");
    },
    handleToPublish() {
      const params = {
        article: Object.assign({}, this.item, {
          status: "publish",
        }),
      };
      this.updateArticle({
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
  },
};
</script>
