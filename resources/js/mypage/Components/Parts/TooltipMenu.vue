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
import { api_handlable } from "../../mixins";
export default {
  props: ["article"],
  mixins: [api_handlable],
  computed: {
    is_publish() {
      return this.article.status === this.$t("statuses.publish");
    },
  },
  methods: {
    handleEdit() {
      this.$router.push({
        name: "editArticle",
        params: { id: this.article.id },
      });
    },
    handleToPrivate() {
      const params = {
        article: Object.assign({}, this.article, {
          status: "private",
        }),
      };
      this.updateArticle(params);
    },
    handleToPublish() {
      const params = {
        article: Object.assign({}, this.article, {
          status: "publish",
        }),
      };
      this.updateArticle(params);
    },
    handleCopy(text) {
      this.$copyText(text);
      this.toastSuccess(this.$t("Copied"));
    },
    setArticles(articles) {
      this.$emit("update:articles", articles);
    },
  },
};
</script>
