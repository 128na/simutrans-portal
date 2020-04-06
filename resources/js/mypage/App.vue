<template>
  <router-view
    v-if="initialized"
    :user="user"
    :articles="articles"
    :attachments="attachments"
    :options="options"
    @update:attachments="handleAttachments"
    @update:articles="handleArticles"
  />
  <div v-else>Loading ...</div>
</template>
<script>
import { DateTime } from "luxon";
import api from "./api";
import { toastable } from "./mixins";
export default {
  data() {
    return {
      initialized: false,
      user: null,
      articles: [],
      attachments: [],
      options: null
    };
  },
  created() {
    this.initialize();
  },
  methods: {
    async initialize() {
      this.initialized = false;

      await Promise.all([
        this.fetchUser(),
        this.fetchArticles(),
        this.fetchAttachments(),
        this.fetchOptions()
      ]);

      this.initialized = true;
    },
    async fetchUser() {
      const res = await api.fetchUser().catch(this.handleErrorToast);

      if (res && res.status === 200) {
        this.user = res.data.data;
      }
    },
    async fetchAttachments() {
      const res = await api.fetchAttachments().catch(this.handleErrorToast);

      if (res && res.status === 200) {
        this.handleAttachments(res.data.data);
      }
    },
    handleAttachments(attachments) {
      this.attachments = attachments;
    },
    async fetchArticles() {
      const res = await api.fetchArticles().catch(this.handleErrorToast);

      if (res && res.status === 200) {
        this.handleArticles(res.data.data);
      }
    },
    handleArticles(articles) {
      this.articles = articles.map(a => {
        a.created_at = DateTime.fromISO(a.created_at).toLocaleString(
          DateTime.DATETIME_FULL
        );
        a.updated_at = DateTime.fromISO(a.updated_at).toLocaleString(
          DateTime.DATETIME_FULL
        );
        return a;
      });
    },
    async fetchOptions() {
      const res = await api.fetchOptions().catch(this.handleErrorToast);

      if (res && res.status === 200) {
        this.options = res.data;
      }
    }
  }
};
</script>
