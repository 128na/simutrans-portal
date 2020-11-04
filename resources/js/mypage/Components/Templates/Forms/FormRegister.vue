<template>
  <b-form>
    <b-form-group>
      <template slot="label">
        <badge-required />
        名前
      </template>
      <b-form-input
        type="text"
        v-model="params.name"
        :state="validationState('name')"
        autocomplete="name"
      />
      <validation-message field="name" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        メールアドレス
      </template>
      <b-form-input
        type="email"
        v-model="params.email"
        :state="validationState('email')"
        autocomplete="email"
      />
      <validation-message field="email" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        パスワード
      </template>
      <input-password
        v-model="params.password"
        :state="validationState('password')"
        autocomplete="new-password"
      />
      <validation-message field="password" />
    </b-form-group>
    <slot />
  </b-form>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  props: ["params"],
  computed: {
    ...mapGetters(["validationState"]),
  },
};
</script>
