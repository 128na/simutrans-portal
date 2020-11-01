<template>
  <div>
    <h1>{{ $t("Login") }}</h1>
    <b-form>
      <b-form-group :label="$t('Email')">
        <b-form-input
          type="email"
          v-model="params.email"
          :state="validationState('password')"
          autocomplete="email"
        />
      </b-form-group>
      <b-form-group :label="$t('Password')">
        <form-password
          v-model="params.password"
          :state="validationState('password')"
          autocomplete="current-password"
        />
      </b-form-group>
      <b-form-group>
        <b-form-checkbox v-model="params.remember">
          {{ $t("Remember Me") }}
        </b-form-checkbox>
      </b-form-group>
      <b-form-group>
        <b-button
          class="mr-1"
          type="submit"
          variant="primary"
          :disabled="fetching"
          @click="handleLogin"
          >{{ $t("Login") }}</b-button
        >
        <router-link :to="to_register">
          {{ $t("Register") }}
        </router-link>
        &nbsp;|&nbsp;
        <router-link :to="to_reset">
          {{ $t("Forgot Your Password?") }}
        </router-link>
      </b-form-group>
    </b-form>
  </div>
</template>
<script>
import { linkable, validateNotLogin } from "../../mixins";
import { mapGetters, mapActions } from "vuex";
export default {
  mixins: [linkable, validateNotLogin],
  data() {
    return {
      params: {
        email: "",
        password: "",
        remember: false,
      },
    };
  },
  methods: {
    ...mapActions(["login"]),
    handleLogin() {
      this.login(this.params);
    },
  },
  computed: {
    ...mapGetters(["fetching", "validationState"]),
  },
};
</script>
