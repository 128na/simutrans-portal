<template>
  <div v-if="copy">
    <button-back />
    <h1>{{$t('Edit {title}', {title:selected_article.title})}}</h1>
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
      <b-btn :disabled="fetching" variant="primary" @click="handleUpdate">{{$t('Save')}}</b-btn>
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
  props: ["articles", "attachments", "options"],
  mixins: [verifiedable, previewable, api_handlable, editor_handlable],
  data() {
    return {
      should_tweet: false
    };
  },
  created() {
    this.setCopy(this.selected_article);
  },
  computed: {
    selected_article() {
      return this.articles.find(a => a.id == this.$route.params.id);
    }
  },
  methods: {
    handlePreview() {
      const params = {
        article: this.copy,
        should_tweet: this.should_tweet,
        preview: true
      };
      this.updateArticle(params);
    },
    handleUpdate() {
      const params = {
        article: this.copy,
        should_tweet: this.should_tweet,
        preview: false
      };
      this.updateArticle(params);
    },
    setArticles(articles) {
      this.$emit("update:articles", articles);
      this.unsetUnloadDialog();
      this.$router.push({ name: "index" });
    },
    getOriginal() {
      return this.selected_article;
    }
  }
};
</script>
