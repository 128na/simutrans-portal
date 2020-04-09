<template>
  <div>
    <b-form-group :label="$t('Avatar image')">
      <media-manager
        name="avatar"
        v-model="user.profile.data.avatar"
        type="Profile"
        :id="user.profile.id"
        :attachments="attachments"
        :only_image="true"
        @update:attachments="$emit('update:attachments', $event)"
      />
    </b-form-group>
    <b-form-group :label="$t('Name')">
      <b-form-input type="text" v-model="user.name" />
    </b-form-group>
    <b-form-group :label="$t('Email')">
      <b-form-input type="email" v-model="user.email" />
    </b-form-group>
    <b-form-group :label="$t('Description')">
      <b-form-textarea v-model="user.profile.data.description" rows="8" />
    </b-form-group>
    <b-form-group :label="$t('Website URL')">
      <b-form-input type="url" v-model="user.profile.data.website" />
    </b-form-group>
    <b-form-group :label="$t('Twitter ID')">
      <b-input-group prepend="@">
        <b-form-input type="text" v-model="user.profile.data.twitter" />
      </b-input-group>
    </b-form-group>
  </div>
</template>
<script>
export default {
  name: "form-profile",
  props: ["user", "attachments"]
};
</script>
