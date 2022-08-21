<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-required />
        作者
      </template>
      <b-form-input v-model="article.contents.author" type="text" :state="validationState('article.contents.author')" />
      <validation-message field="article.contents.author" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        リンク先URL
      </template>
      <b-form-input v-model="article.contents.link" type="url" :state="validationState('article.contents.link')" />
      <validation-message field="article.contents.link" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        説明
      </template>
      <countable-textarea v-model="article.contents.description"
        :state="validationState('article.contents.description')" :rows="8" :max-length="2048" />
      <validation-message field="article.contents.description" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        謝辞・参考にしたアドオン
      </template>
      <countable-textarea v-model="article.contents.thanks" :state="validationState('article.contents.thanks')"
        :rows="8" :max-length="2048" />
      <validation-message field="article.contents.thanks" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        掲載許可
      </template>
      <b-form-checkbox v-model="article.contents.agreement" :state="validationState('article.contents.agreement')">
        この記事は作者の許可を得てまたは作者自身により掲載しています。
      </b-form-checkbox>
      <validation-message field="article.contents.agreement" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        自動リンク切れチェック
      </template>
      <b-form-checkbox v-model="article.contents.exclude_link_check"
        :state="validationState('article.contents.exclude_link_check')">
        自動リンク切れチェックの対象外にする
      </b-form-checkbox>
      <validation-message field="article.contents.exclude_link_check" />
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
