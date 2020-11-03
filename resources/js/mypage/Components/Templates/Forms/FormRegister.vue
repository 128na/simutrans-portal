<template>
  <b-form>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{ $t("Name") }}
      </template>
      <b-form-input
        type="text"
        v-model="params.name"
        :state="validationState('name')"
        autocomplete="name"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{ $t("Email") }}
      </template>
      <b-form-input
        type="email"
        v-model="params.email"
        :state="validationState('email')"
        autocomplete="email"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{ $t("Password") }}
      </template>
      <input-password
        v-model="params.password"
        :state="validationState('password')"
        autocomplete="new-password"
      />
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
