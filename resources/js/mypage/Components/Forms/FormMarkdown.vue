<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{$t('Contents')}}
      </template>
      <b-form-textarea
        v-model="article.contents.markdown"
        :state="state('article.contents.markdown')"
        rows="20"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{$t('Categories')}}
      </template>
      <b-form-checkbox-group v-model="article.categories" :options="options.categories.page" />
    </b-form-group>
  </div>
</template>
<script>
import { validatable } from "../../mixins";
export default {
  name: "form-markdown",
  props: ["article", "options"],
  mixins: [validatable]
};
</script>
