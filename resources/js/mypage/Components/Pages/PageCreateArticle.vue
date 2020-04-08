<template>
  <div v-if="$route.params.post_type">
    <button-back />
    <h1>Create Article</h1>
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
      <b-btn :disabled="fetching" variant="primary" @click="handleCreate">Create</b-btn>
    </b-form-group>
  </div>
</template>
<script>
import { verifiedable, previewable, api_handlable } from "../../mixins";
export default {
  props: ["attachments", "options"],
  data() {
    return {
      article: null,
      should_tweet: true
    };
  },
  mixins: [verifiedable, previewable, api_handlable],
  created() {
    switch (this.$route.params.post_type) {
      case "addon-post":
        return this.createAddonPost();
      case "addon-introduction":
        return this.createAddonIntroduction();
      case "page":
        return this.createPage();
    }
  },
  methods: {
    createAddonPost() {
      this.article = {
        post_type: "addon-post",
        title: "",
        slug: "",
        status: null,
        contents: {
          thumbnail: null,
          author: "",
          description: "",
          file: null,
          license: "",
          thanks: ""
        },
        categories: [],
        tags: []
      };
    },
    createAddonIntroduction() {
      this.article = {
        post_type: "addon-introduction",
        title: "",
        slug: "",
        status: null,
        contents: {
          thumbnail: null,
          agreement: false,
          author: "",
          description: "",
          license: "",
          link: "",
          thanks: ""
        },
        categories: [],
        tags: []
      };
    },
    createPage() {
      this.article = {
        post_type: "page",
        title: "",
        slug: "",
        status: null,
        contents: {
          thumbnail: null,
          sections: []
        },
        categories: []
      };
    },
    handlePreview() {
      const params = {
        article: this.article,
        should_tweet: this.should_tweet,
        preview: true
      };
      this.createArticle(params);
    },
    handleCreate() {
      const params = {
        article: this.article,
        should_tweet: this.should_tweet,
        preview: false
      };
      this.createArticle(params);
    },
    setArticles(articles) {
      this.$emit("update:articles", articles);
      this.$router.push({ name: "index" });
    }
  }
};
</script>
