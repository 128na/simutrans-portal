<template>
  <transition appear name="fade" mode="out-in">
    <div v-if="initialized" key="initialized">
      <header-menu :user="user" @logout="handleLogout" />
      <b-container class="my-4">
        <transition name="fade" mode="out-in">
          <router-view
            :user="user"
            :articles="articles"
            :attachments="attachments"
            :options="options"
            @update:attachments="handleAttachments"
            @update:articles="handleArticles"
            @update:user="handleUser"
            @loggedin="handleLoggedin"
          />
        </transition>
      </b-container>
    </div>
    <div v-else class="m-auto" key="not_initialized">
      <h2>
        <b-icon icon="arrow-clockwise" animation="spin" class="mr-2"></b-icon>
        {{$t('Loading...')}}
      </h2>
    </div>
  </transition>
</template>
<script>
import { DateTime } from "luxon";
import { toastable, api_handlable } from "./mixins";
export default {
  mixins: [toastable, api_handlable],
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
      this.user = null;
      this.articles = [];
      this.attachments = [];
      this.options = null;

      await this.fetchUser();

      this.initialized = true;
    },
    async handleLoggedin(user) {
      this.user = user;

      await Promise.all([
        this.fetchAttachments(),
        this.fetchArticles(),
        this.fetchOptions()
      ]);
      this.$router.push({ name: "index" });
      this.toastSuccess("Logged in");
    },
    handleLogout() {
      this.initialize();
    },
    async setUser(user) {
      this.user = user;

      await Promise.all([
        this.fetchAttachments(),
        this.fetchArticles(),
        this.fetchOptions()
      ]);
    },
    setAttachments(attachments) {
      this.attachments = attachments;
    },
    setArticles(articles) {
      this.articles = articles.map(a =>
        Object.assign(a, {
          created_at: DateTime.fromISO(a.created_at),
          updated_at: DateTime.fromISO(a.updated_at)
        })
      );
    },
    setOptions(options) {
      this.options = options;
    },
    handleUser(user) {
      this.toastSuccess("Profile Updated.");
      this.setUser(user);
    },
    handleAttachments(attachments) {
      this.setAttachments(attachments);
    },
    handleArticles(articles) {
      this.toastSuccess("Article Updated");
      this.setArticles(articles);
    }
  }
};
</script>
<style>
.pre-line {
  white-space: pre-line;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.1s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}
.clickable {
  cursor: pointer;
}
:disabled {
  cursor: not-allowed;
}
a.dropdown-item,
a.btn,
button.btn {
  display: inline-flex;
  align-items: center;
}
</style>
