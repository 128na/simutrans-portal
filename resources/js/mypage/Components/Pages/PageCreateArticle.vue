<template>
  <div>
    <router-link :to="{name:'index'}">Back to MyPage</router-link>
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
      <b-btn @click="handlePreview">Preview</b-btn>
      <b-btn variant="primary" @click="handleUpdateOrCreate">Create</b-btn>
    </b-form-group>
  </div>
</template>
<script>
import { article_editable } from "../../mixins";
export default {
  props: ["attachments", "options"],
  mixins: [article_editable],
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
        tags: [],
        should_tweet: true
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
        tags: [],
        should_tweet: true
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
        tags: [],
        should_tweet: true
      };
    }
  }
};
</script>
