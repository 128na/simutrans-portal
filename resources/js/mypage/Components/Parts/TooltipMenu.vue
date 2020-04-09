<template>
  <b-dropdown right :no-caret="true" variant="outline-secondary" size="sm">
    <template v-slot:button-content>
      <b-icon icon="three-dots-vertical" />
    </template>
    <b-dropdown-item v-if="is_publish" :href="article.url" target="_blank">
      <b-icon icon="box-arrow-up-right" class="mr-1" />Show
    </b-dropdown-item>
    <b-dropdown-item @click="handleEdit">
      <b-icon icon="pencil" class="mr-1" />Edit
    </b-dropdown-item>
  </b-dropdown>
</template>
<script>
export default {
  props: ["article"],
  computed: {
    is_publish() {
      return this.article.status === this.$t("statuses.publish");
    }
  },
  methods: {
    handleEdit() {
      this.$router.push({
        name: "editArticle",
        params: { id: this.article.id }
      });
    }
  }
};
</script>
