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
      <b-btn variant="primary" @click="handleUpdateOrCreate">Update</b-btn>
    </b-form-group>
  </div>
</template>
<script>
import { article_editable } from "../../mixins";
export default {
  props: ["articles", "attachments", "options"],
  mixins: [article_editable],
  created() {
    this.article = this.articles.find(a => a.id == this.$route.params.id);
  }
};
</script>
