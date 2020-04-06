<template>
  <div>
    <div>
      <p>
        Create:&nbsp;
        <router-link :to="to_addon_post">Addon Post</router-link>,&nbsp;
        <router-link :to="to_addon_introduction">Addon Introduction</router-link>,&nbsp;
        <router-link :to="to_page">Page</router-link>
      </p>
      <p>
        Profile:&nbsp;
        <router-link :to="to_profile">Edit</router-link>
      </p>
      <p>
        Analytics:&nbsp;
        <router-link :to="to_analytics">Show</router-link>
      </p>
    </div>

    <article-table :articles="articles" />
  </div>
</template>
<script>
export default {
  props: ["articles"],
  computed: {
    to_addon_post() {
      return {
        name: "createArticle",
        params: { post_type: "addon-post" }
      };
    },
    to_addon_introduction() {
      return {
        name: "createArticle",
        params: { post_type: "addon-introduction" }
      };
    },
    to_page() {
      return {
        name: "createArticle",
        params: { post_type: "page" }
      };
    },
    to_profile() {
      return {
        name: "editProfile"
      };
    },
    to_analytics() {
      return {
        name: "analyticsArticle"
      };
    }
  }
};
</script>
