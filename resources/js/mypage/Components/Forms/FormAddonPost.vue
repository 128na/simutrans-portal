<template>
  <div>
    <b-form-group label="author">
      <b-form-input type="text" v-model="article.contents.author" />
    </b-form-group>
    <b-form-group label="addon file">
      <media-manager
        name="addon"
        v-model="article.contents.file"
        type="Article"
        :id="article.id"
        :attachments="attachments"
        @update:attachments="$emit('update:attachments', $event)"
      />
    </b-form-group>
    <b-form-group label="description">
      <b-form-textarea v-model="article.contents.description" rows="8" />
    </b-form-group>
    <b-form-group label="thanks">
      <b-form-textarea v-model="article.contents.thanks" rows="8" />
    </b-form-group>
  </div>
</template>
<script>
export default {
  name: "form-addon-post",
  props: ["article", "attachments", "options"]
};
</script>
