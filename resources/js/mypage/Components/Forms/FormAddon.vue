<template>
  <div>
    <b-form-group :label="$t('Paks')">
      <b-form-checkbox-group v-model="article.categories" :options="options.categories.pak" />
    </b-form-group>
    <b-form-group :label="$t('Addon Types')">
      <b-form-checkbox-group v-model="article.categories" :options="options.categories.addon" />
    </b-form-group>
    <b-form-group v-show="includes_pak128" :label="$t('Track positions for pak128')">
      <b-form-checkbox-group
        v-model="article.categories"
        :options="options.categories.pak128_position"
      />
    </b-form-group>
    <b-form-group :label="$t('Tags')">
      <tag-selector v-model="article.tags" :creatable="true" />
    </b-form-group>
    <b-form-group :label="$t('License')">
      <b-form-checkbox-group v-model="article.categories" :options="options.categories.license" />
    </b-form-group>
    <b-form-group :label="$t('License other')">
      <b-form-textarea v-model="article.license" />
    </b-form-group>
  </div>
</template>
<script>
export default {
  name: "form-addon",
  props: ["article", "options"],
  computed: {
    pak128_category_id() {
      const pak = this.options.categories.pak;
      return pak.find(c => c.text == "Pak128").value || null;
    },
    includes_pak128() {
      return this.article.categories.includes(this.pak128_category_id);
    }
  }
};
</script>
