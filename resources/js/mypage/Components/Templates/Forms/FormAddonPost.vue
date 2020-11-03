<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{ $t("Author") }}
      </template>
      <b-form-input
        type="text"
        v-model="article.contents.author"
        :state="validationState('article.contents.author')"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{ $t("Addon File") }}
      </template>
      <media-manager
        name="addon"
        v-model="article.contents.file"
        type="Article"
        :id="article.id"
        :state="validationState('article.contents.file')"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{ $t("Description") }}
      </template>
      <b-form-textarea
        v-model="article.contents.description"
        :state="validationState('article.contents.description')"
        rows="8"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{ $t("Acknowledgments and Referenced") }}
      </template>
      <b-form-textarea
        v-model="article.contents.thanks"
        :state="validationState('article.contents.thanks')"
        rows="8"
      />
    </b-form-group>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  name: "form-addon-post",
  props: ["article"],
  computed: {
    ...mapGetters(["validationState"]),
  },
};
</script>
