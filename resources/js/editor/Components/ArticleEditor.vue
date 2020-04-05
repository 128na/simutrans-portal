<template>
  <div v-if="initialized">
    <b-form-group label="post type" v-if="can_select_post_type">
      <b-form-select v-model="article.post_type" :options="options.post_types" />
    </b-form-group>

    <component
      v-if="article.post_type"
      :is="component_name"
      :article="article"
      :attachments="attachments"
      :options="options"
      @attachmentsUpdated="handleAttachmentsUpdated"
    />

    <b-form-group label="post type" v-show="can_save">
      <b-form-checkbox v-model="article.should_tweet">Should Tweet</b-form-checkbox>
    </b-form-group>
    <b-form-group>
      <b-btn :disabled="!can_save" @click="handlePreview">Preview</b-btn>
      <b-btn :disabled="!can_save" variant="primary" @click="handleSave">Save</b-btn>
    </b-form-group>
  </div>
  <div v-else>initializing...</div>
</template>
<script>
import api from "../api";
import { toastable } from "../mixins";
const initialArticle = {
  post_type: null,
  title: "",
  slug: "",
  status: null,
  contents: {
    thumbnail: null,
    agreement: false,
    author: "",
    description: "",
    file: null,
    license: "",
    link: "",
    thanks: "",
    sections: []
  },
  categories: [],
  tags: [],
  should_tweet: true
};

export default {
  name: "article-editor",
  mixins: [toastable],
  data() {
    return {
      initialized: false,
      can_select_post_type: false,
      article: {},
      options: {
        categories: {},
        statuses: [],
        post_types: []
      },
      attachments: [],
      preview_window: null
    };
  },
  created() {
    this.initialize();
  },
  computed: {
    component_name() {
      return this.article.post_type;
    },
    can_save() {
      return this.article.post_type;
    }
  },
  methods: {
    async initialize() {
      this.initialized = false;

      this.fetchArticle();
      await Promise.all([this.fetchOptions(), this.fetchAttachments()]);

      this.initialized = true;
    },
    async fetchOptions() {
      const res = await api.fetchOptions().catch(this.handleErrorToast);

      if (res && res.status === 200) {
        this.options = res.data;
      }
    },
    async fetchAttachments() {
      const res = await api
        .fetchAttachments(this.article.id)
        .catch(this.handleErrorToast);

      if (res && res.status === 200) {
        this.attachments = res.data.data;
      }
    },
    fetchArticle() {
      const article = api.fetchArticle();

      this.can_select_post_type = !article;
      this.article = article || initialArticle;
    },
    handleAttachmentsUpdated(attachments) {
      this.attachments = attachments;
    },
    async handlePreview() {
      const html = await (this.article.id
        ? this.update(true)
        : this.create(true));
      if (html) {
        if (!this.preview_window || this.preview_window.closed) {
          this.preview_window = window.open();
        }
        this.preview_window.document.body.innerHTML = html;
      }
    },
    async handleSave() {
      const data = await (this.article.id ? this.update() : this.create());
      if (data) {
        this.toastSuccess(
          this.article.id ? "Article Updated" : "Article Created"
        );
        this.article = data.data;
      }
    },
    async create(preview = false) {
      const res = await api
        .createArticle(this.article, preview)
        .catch(this.handleErrorToast);
      if (res && res.status === 200) {
        return res.data;
      }
    },
    async update(preview = false) {
      const res = await api
        .updateArticle(this.article, preview)
        .catch(this.handleErrorToast);
      if (res && res.status === 200) {
        return res.data;
      }
    }
  }
};
</script>
<style lang="scss">
.pre-line {
  white-space: pre-line;
}
</style>
