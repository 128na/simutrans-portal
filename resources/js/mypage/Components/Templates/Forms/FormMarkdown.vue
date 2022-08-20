<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-required />
        コンテンツ
      </template>
      <countable-textarea v-model="article.contents.markdown" :state="validationState('article.contents.markdown')"
        :rows="20" :max-length="65535" />
      <validation-message field="article.contents.markdown" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        カテゴリ
      </template>
      <b-form-checkbox-group v-model="selectedCategories" :options="page" />
    </b-form-group>
  </div>
</template>
<script>
import { mapGetters } from 'vuex';
export default {
  props: ['article'],
  computed: {
    ...mapGetters(['options', 'validationState', 'getCategory']),
    page() {
      return this.options.categories.page.map(c => Object.create({ text: c.name, value: c.id }));
    },
    selectedCategories:
    {
      get() {
        return this.article.categories.map(c => c.id);
      },
      set(v) {
        this.article.categories = v.map(c => this.getCategory(c));
      }
    }
  }
};
</script>
