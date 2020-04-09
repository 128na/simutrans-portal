<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-optional />
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
        {{$t('Addon File')}}
      </template>
      <media-manager
        name="addon"
        v-model="article.contents.file"
        type="Article"
        :id="article.id"
        :attachments="attachments"
        :state="state('article.contents.file')"
        @update:attachments="$emit('update:attachments', $event)"
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
  </div>
</template>
<script>
import { validatable } from "../../mixins";
export default {
  name: "form-addon-post",
  props: ["article", "attachments", "options"],
  mixins: [validatable]
};
</script>
