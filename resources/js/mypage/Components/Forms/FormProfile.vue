<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{ $t("Avatar image") }}
      </template>
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
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{ $t("Name") }}
      </template>
      <b-form-input
        type="text"
        v-model="user.name"
        :state="state('user.name')"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{ $t("Email") }}
      </template>
      <b-form-input
        type="email"
        v-model="user.email"
        :state="state('user.email')"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{ $t("Description") }}
      </template>
      <b-form-textarea
        v-model="user.profile.data.description"
        rows="8"
        :state="state('user.profile.data.description')"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{ $t("Website URL") }}
      </template>
      <b-form-input
        type="url"
        v-model="user.profile.data.website"
        :state="state('user.profile.data.website')"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-optional />
        {{ $t("Twitter ID") }}
      </template>
      <b-input-group prepend="@">
        <b-form-input
          type="text"
          v-model="user.profile.data.twitter"
          :state="state('user.profile.data.twitter')"
        />
      </b-input-group>
    </b-form-group>
  </div>
</template>
<script>
import { validatable } from "../../mixins";
export default {
  name: "form-profile",
  props: ["user", "attachments"],
  mixins: [validatable],
};
</script>
