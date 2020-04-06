<template>
  <div>
    <b-form-group label="author">
      <b-form-input type="text" v-model="article.contents.author" />
    </b-form-group>
    <b-form-group label="link">
      <b-form-input type="url" v-model="article.contents.link" />
    </b-form-group>
    <b-form-group label="description">
      <b-form-textarea v-model="article.contents.description" rows="8" />
    </b-form-group>
    <b-form-group label="thanks">
      <b-form-textarea v-model="article.contents.thanks" rows="8" />
    </b-form-group>
    <b-form-group label="agreement">
      <b-form-checkbox v-model="article.contents.agreement">agreement</b-form-checkbox>
    </b-form-group>
  </div>
</template>
<script>
export default {
  name: "form-addon-introduction",
  props: ["article", "options"]
};
</script>
