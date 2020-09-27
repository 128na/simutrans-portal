<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{ $t("Paks") }}
      </template>
      <b-form-checkbox-group
        v-model="article.categories"
        :options="options.categories.pak"
      />
    </b-form-group>
    <b-form-group :label="$t('s')">
      <template slot="label">
        <badge-optional />
        {{ $t("Addon Type") }}
      </template>
      <b-form-checkbox-group
        v-model="article.categories"
        :options="options.categories.addon"
      />
    </b-form-group>
    <b-form-group v-show="includes_pak128">
      <template slot="label">
        <badge-optional />
        {{ $t("Track positions for pak128") }}
      </template>
      <b-form-checkbox-group
        v-model="article.categories"
        :options="options.categories.pak128_position"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{ $t("Tags") }}
      </template>
      <tag-selector v-model="article.tags" :creatable="true" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{ $t("License") }}
      </template>
      <b-form-checkbox-group
        v-model="article.categories"
        :options="options.categories.license"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{ $t("License other") }}
      </template>
      <b-form-textarea
        v-model="article.contents.license"
        :state="state('article.contents.license')"
      />
    </b-form-group>
  </div>
</template>
<script>
import { validatable } from "../../mixins";
export default {
  name: "form-addon",
  props: ["article", "options"],
  mixins: [validatable],
  computed: {
    pak128_category_id() {
      const pak = this.options.categories.pak;
      return pak.find((c) => c.text == "Pak128").value || null;
    },
    includes_pak128() {
      return this.article.categories.includes(this.pak128_category_id);
    },
  },
};
</script>
