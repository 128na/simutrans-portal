<template>
  <div>
    <h1>My Page</h1>
    <div v-if="!user.verified">
      <form-verify :user="user" />
    </div>
    <div>
      <p>
        <b-button variant="primary" :to="to_analytics">Analytics</b-button>
      </p>
    </div>

    <article-table v-show="articles.length" :articles="articles" :user="user" />
  </div>
</template>
<script>
export default {
  props: ["articles", "user"],
  computed: {
    to_analytics() {
      return {
        name: "analyticsArticle"
      };
    }
  }
};
</script>
