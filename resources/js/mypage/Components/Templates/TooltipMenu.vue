<template>
  <b-dropdown right :no-caret="true" variant="outline-secondary" size="sm">
    <template v-slot:button-content>
      <b-icon icon="three-dots-vertical" />
    </template>
    <b-dropdown-item
      v-if="is_publish"
      class="mb-1"
      @click="handleCopy(article.url)"
    >
      <b-icon icon="clipboard-data" class="mr-1" />
      {{ $t("Copy URL") }}
    </b-dropdown-item>
    <b-dropdown-item
      v-if="is_publish"
      :href="article.url"
      target="_blank"
      class="mb-1"
    >
      <b-icon icon="box-arrow-up-right" class="mr-1" />
      {{ $t("Show") }}
    </b-dropdown-item>
    <b-dropdown-item @click="handleEdit">
      <b-icon icon="pencil" class="mr-1" />
      {{ $t("Edit") }}
    </b-dropdown-item>
    <b-dropdown-divider></b-dropdown-divider>
    <b-dropdown-item v-show="is_publish" @click="handleToPrivate">
      <b-icon icon="lock-fill" class="mr-1" />
      {{ $t("To Private") }}
    </b-dropdown-item>
    <b-dropdown-item v-show="!is_publish" @click="handleToPublish">
      <b-icon icon="unlock-fill" class="mr-1" />
      {{ $t("To Publish (no tweet)") }}
    </b-dropdown-item>
  </b-dropdown>
</template>
<script>
import { mapActions } from "vuex";
export default {
  props: ["article"],
  computed: {
    is_publish() {
      return this.article.status === this.$t("statuses.publish");
    },
  },
  methods: {
    ...mapActions(["updateArticle", "setInfoMessage"]),
    handleEdit() {
      this.goto(this.route_edit_article(this.article.id));
    },
    handleToPrivate() {
      const params = {
        article: Object.assign({}, this.article, {
          status: "private",
        }),
      };
      this.$store.dispatch("updateArticle", {
        params,
        message: "ステータスを非公開にしました",
      });
    },
    handleToPublish() {
      const params = {
        article: Object.assign({}, this.article, {
          status: "publish",
        }),
      };
      this.$store.dispatch("updateArticle", {
        params,
        message: "ステータスを公開にしました",
      });
    },
    handleCopy(text) {
      this.$copyText(text);
      this.$store.dispatch("setInfoMessage", {
        message: "クリップボードにコピーしました",
      });
    },
  },
};
</script>
