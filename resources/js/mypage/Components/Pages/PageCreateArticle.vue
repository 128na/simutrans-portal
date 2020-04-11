<template>
  <div v-if="$route.params.post_type">
    <button-back />
    <h1>{{title}}</h1>
    <component
      :is="copy.post_type"
      :article="copy"
      :attachments="attachments"
      :options="options"
      :errors="errors"
      @update:attachments="$emit('update:attachments', $event)"
    />

    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{$t('Auto Tweet')}}
      </template>
      <b-form-checkbox v-model="should_tweet">{{$t('Tweet when posting or updating.')}}</b-form-checkbox>
    </b-form-group>
    <b-form-group>
      <b-btn :disabled="fetching" @click="handlePreview">{{$t('Preview')}}</b-btn>
      <b-btn :disabled="fetching" variant="primary" @click="handleCreate">{{$t('Save')}}</b-btn>
    </b-form-group>
  </div>
</template>
<script>
import {
  verifiedable,
  previewable,
  api_handlable,
  editor_handlable
} from "../../mixins";
export default {
  props: ["attachments", "options"],
  data() {
    return {
      article: null,
      should_tweet: true
    };
  },
  mixins: [verifiedable, previewable, api_handlable, editor_handlable],
  created() {
    this.initialize();
  },
  watch: {
    "$route.params.post_type"() {
      this.initialize();
    }
  },
  computed: {
    title() {
      return this.$t("Create {post_type}", {
        post_type: this.$t(`post_types.${this.article.post_type}`)
      });
    }
  },
  methods: {
    initialize() {
      switch (this.$route.params.post_type) {
        case "addon-post":
          this.createAddonPost();
          break;
        case "addon-introduction":
          this.createAddonIntroduction();
          break;
        case "page":
          this.createPage();
          break;
        case "markdown":
          this.createMarkdown();
          break;
      }
      this.setCopy(this.article);
    },
    createAddonPost() {
      this.article = {
        post_type: "addon-post",
        title: "",
        slug: "",
        status: "draft",
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
        status: "draft",
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
        status: "draft",
        contents: {
          thumbnail: null,
          sections: [{ type: "text", text: "" }]
        },
        categories: []
      };
    },
    createMarkdown() {
      this.article = {
        post_type: "markdown",
        title: "",
        slug: "",
        status: "draft",
        contents: {
          thumbnail: null,
          markdown: ""
        },
        categories: []
      };
    },
    handlePreview() {
      const params = {
        article: this.copy,
        should_tweet: this.should_tweet,
        preview: true
      };
      this.createArticle(params);
    },
    handleCreate() {
      const params = {
        article: this.copy,
        should_tweet: this.should_tweet,
        preview: false
      };
      this.createArticle(params);
    },
    setArticles(articles) {
      this.$emit("update:articles", articles);
      this.unsetUnloadDialog();
      this.$router.push({ name: "index" });
    },
    getOriginal() {
      return this.article;
    }
  }
};
</script>
