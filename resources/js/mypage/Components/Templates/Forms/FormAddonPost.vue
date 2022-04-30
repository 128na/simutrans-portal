<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        作者
      </template>
      <b-form-input
        v-model="article.contents.author"
        type="text"
        :state="validationState('article.contents.author')"
      />
      <validation-message field="article.contents.author" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        アドオンファイル
      </template>
      <media-manager
        :id="article.id"
        v-model="article.contents.file"
        name="addon"
        type="Article"
        :state="validationState('article.contents.file')"
      />
      <validation-message field="article.contents.file" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        説明
      </template>
      <b-form-textarea
        v-model="article.contents.description"
        :state="validationState('article.contents.description')"
        rows="8"
      />
      <validation-message field="article.contents.description" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        謝辞・参考にしたアドオン
      </template>
      <b-form-textarea
        v-model="article.contents.thanks"
        :state="validationState('article.contents.thanks')"
        rows="8"
      />
      <validation-message field="article.contents.thanks" />
    </b-form-group>
  </div>
</template>
<script>
import { mapGetters } from 'vuex';
export default {
  props: ['article'],
  computed: {
    ...mapGetters(['validationState'])
  }
};
</script>
