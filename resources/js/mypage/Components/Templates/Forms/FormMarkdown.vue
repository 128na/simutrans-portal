<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-required />
        コンテンツ
      </template>
      <b-form-textarea
        v-model="article.contents.markdown"
        :state="validationState('article.contents.markdown')"
        rows="20"
      />
      <validation-message field="article.contents.markdown" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        カテゴリ
      </template>
      <b-form-checkbox-group
        v-model="article.categories"
        :options="options.categories.page"
      />
    </b-form-group>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  props: ["article"],
  computed: {
    ...mapGetters(["options", "validationState"]),
  },
};
</script>
