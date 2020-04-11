<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{$t('Author')}}
      </template>
      <b-form-input
        type="text"
        v-model="article.contents.author"
        :state="state('article.contents.author')"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{$t('Link')}}
      </template>
      <b-form-input
        type="url"
        v-model="article.contents.link"
        :state="state('article.contents.link')"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{$t('Description')}}
      </template>
      <b-form-textarea
        v-model="article.contents.description"
        :state="state('article.contents.description')"
        rows="8"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{$t('Acknowledgments and Referenced')}}
      </template>
      <b-form-textarea
        v-model="article.contents.thanks"
        :state="state('article.contents.thanks')"
        rows="8"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{$t('Agreement')}}
      </template>
      <b-form-checkbox
        v-model="article.contents.agreement"
        :state="state('article.contents.agreement')"
      >
        {{
        $t("This article is published by author's permission or by author himself.")
        }}
      </b-form-checkbox>
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{$t('Periodic broken link checks')}}
      </template>
      <b-form-checkbox
        v-model="article.contents.exclude_link_check"
        :state="state('article.contents.exclude_link_check')"
      >
        {{
        $t("Exclude periodic broken link checks")
        }}
      </b-form-checkbox>
    </b-form-group>
  </div>
</template>
<script>
import { validatable } from "../../mixins";
export default {
  name: "form-addon-introduction",
  props: ["article", "options"],
  mixins: [validatable]
};
</script>
