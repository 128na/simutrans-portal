<template>
  <div>
    <h1>My Page</h1>
    <div>
      <p>
        <b-button variant="primary" :to="to_analytics">Analytics</b-button>
      </p>
    </div>

    <article-table :articles="articles" />
  </div>
</template>
<script>
export default {
  props: ["articles"],
  computed: {
    to_analytics() {
      return {
        name: "analyticsArticle"
      };
    }
  }
};
</script>
