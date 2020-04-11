<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{$t('Markdown content')}}
      </template>
      <b-form-textarea
        v-model="article.contents.markdown"
        :state="state('article.contents.markdown')"
        rows="20"
      />
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
