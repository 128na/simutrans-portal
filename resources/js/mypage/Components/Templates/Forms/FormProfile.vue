<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        アバター画像
      </template>
      <media-manager
        name="avatar"
        v-model="user.profile.data.avatar"
        type="Profile"
        :id="user.profile.id"
        :only_image="true"
      />
      <validation-message field="avatar" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        名前
      </template>
      <b-form-input
        type="text"
        v-model="user.name"
        :state="validationState('user.name')"
      />
      <validation-message field="user.name" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        メールアドレス
      </template>
      <b-form-input
        type="email"
        v-model="user.email"
        :state="validationState('user.email')"
      />
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
      <b-form-textarea
        v-model="user.profile.data.description"
        rows="8"
        :state="validationState('user.profile.data.description')"
      />
      <validation-message field="user.profile.data.description" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        WebサイトURL
      </template>
      <b-form-input
        type="url"
        v-model="user.profile.data.website"
        :state="validationState('user.profile.data.website')"
      />
      <validation-message field="user.profile.data.website" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        Twitter ID
      </template>
      <b-input-group prepend="@">
        <b-form-input
          type="text"
          v-model="user.profile.data.twitter"
          :state="validationState('user.profile.data.twitter')"
        />
      </b-input-group>
      <validation-message field="user.profile.data.twitter" />
    </b-form-group>
    <slot />
  </div>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  props: ["user"],
  computed: {
    ...mapGetters(["validationState"]),
  },
};
</script>
