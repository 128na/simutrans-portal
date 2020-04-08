<template>
  <div v-if="article">
    <button-back />
    <h1>Edit Article</h1>
    <component
      :is="article.post_type"
      :article="article"
      :attachments="attachments"
      :options="options"
      @update:attachments="$emit('update:attachments', $event)"
    />

    <b-form-group label="Tweet">
      <b-form-checkbox v-model="should_tweet">Should Tweet</b-form-checkbox>
    </b-form-group>
    <b-form-group>
      <b-btn :disabled="fetching" @click="handlePreview">Preview</b-btn>
      <b-btn :disabled="fetching" variant="primary" @click="handleUpdate">Update</b-btn>
    </b-form-group>
  </div>
</template>
<script>
import { verifiedable, previewable, api_handlable } from "../../mixins";
export default {
  props: ["articles", "attachments", "options"],
  mixins: [verifiedable, previewable, api_handlable],
  data() {
    return {
      article: null,
      should_tweet: false
    };
  },
  created() {
    this.article = this.articles.find(a => a.id == this.$route.params.id);
  },
  methods: {
    handlePreview() {
      const params = {
        article: this.article,
        should_tweet: this.should_tweet,
        preview: true
      };
      this.updateArticle(params);
    },
    handleUpdate() {
      const params = {
        article: this.article,
        should_tweet: this.should_tweet,
        preview: false
      };
      this.updateArticle(params);
    },
    setArticles(articles) {
      this.$emit("update:articles", articles);
      this.$router.push({ name: "index" });
    }
  }
};
</script>
