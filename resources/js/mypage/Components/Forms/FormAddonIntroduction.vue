<template>
  <div>
    <b-form-group :label="$t('Author')">
      <b-form-input type="text" v-model="article.contents.author" />
    </b-form-group>
    <b-form-group :label="$t('Link')">
      <b-form-input type="url" v-model="article.contents.link" />
    </b-form-group>
    <b-form-group :label="$t('Description')">
      <b-form-textarea v-model="article.contents.description" rows="8" />
    </b-form-group>
    <b-form-group :label="$t('Acknowledgments and Referenced')">
      <b-form-textarea v-model="article.contents.thanks" rows="8" />
    </b-form-group>
    <b-form-group :label="$t('Agreement')">
      <b-form-checkbox v-model="article.contents.agreement">
        {{
        $t("This article is published by author's permission or by author himself.")
        }}
      </b-form-checkbox>
    </b-form-group>
  </div>
</template>
<script>
export default {
  name: "form-addon-introduction",
  props: ["article", "options"]
};
</script>
