<template>
  <div>
    <b-form-group :label="$t('Email')">
      <b-form-input
        type="email"
        v-model="params.email"
        :state="state('email')"
        autocomplete="email"
      />
    </b-form-group>
    <b-form-group :label="$t('Password')">
      <form-password
        v-model="params.password"
        :state="state('password')"
        autocomplete="current-password"
      />
    </b-form-group>
    <b-form-group>
      <b-form-checkbox v-model="params.remember" :state="state('remember')">{{
        $t("Remember Me")
      }}</b-form-checkbox>
    </b-form-group>
  </div>
</template>
<script>
import { validatable } from "../../mixins";
export default {
  props: ["params"],
  mixins: [validatable],
};
</script>
