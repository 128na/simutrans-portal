<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        アバター画像
      </template>
      <media-manager :id="user.profile.id" v-model="user.profile.data.avatar" name="avatar" type="Profile"
        :only_image="true" />
      <validation-message field="avatar" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        名前
      </template>
      <b-form-input v-model="user.name" type="text" :state="validationState('user.name')" />
      <validation-message field="user.name" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        メールアドレス
      </template>
      <b-form-input v-model="user.email" type="email" :state="validationState('user.email')" />
      <small>
        メールアドレスを変更すると新しいメールアドレスへ確認メールが送られます。
      </small>
      <validation-message field="user.email" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        説明
      </template>
      <countable-textarea v-model="user.profile.data.description" rows=8 :max-length="1024"
        :state="validationState('user.profile.data.description')" />
      <validation-message field="user.profile.data.description" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        WebサイトURL
      </template>
      <b-form-input v-model="user.profile.data.website" type="url"
        :state="validationState('user.profile.data.website')" />
      <validation-message field="user.profile.data.website" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        Twitter ID
      </template>
      <b-input-group prepend="@">
        <b-form-input v-model="user.profile.data.twitter" type="text"
          :state="validationState('user.profile.data.twitter')" />
      </b-input-group>
      <validation-message field="user.profile.data.twitter" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        Google Tag Manager
      </template>
      <b-form-input v-model="user.profile.data.gtag" type="text" placeholder="UA-00000000, G-00000000"
        :state="validationState('user.profile.data.gtag')" />
      <validation-message field="user.profile.data.gtag" />
      <small>
        自身の投稿記事にGoogleAnalyticsのタグ(GA_Measurement_ID)を追加できます。<br>
        紹介記事のURL遷移数、投稿記事のDL数が計測できます。
      </small>
    </b-form-group>
    <slot />
  </div>
</template>
<script>
import { mapGetters } from 'vuex';
export default {
  props: ['user'],
  computed: {
    ...mapGetters(['validationState'])
  }
};
</script>
