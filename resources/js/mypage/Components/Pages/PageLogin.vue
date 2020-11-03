<template>
  <div>
    <h1>{{ $t("Login") }}</h1>
    <form-login :params="params">
      <b-form-group>
        <fetching-overlay class="mr-2">
          <b-button type="submit" variant="primary" @click="handleLogin">
            {{ $t("Login") }}
          </b-button>
        </fetching-overlay>
        <router-link :to="route_register">
          {{ $t("Register") }}
        </router-link>
        &nbsp;|&nbsp;
        <router-link :to="route_password_reset">
          {{ $t("Forgot Your Password?") }}
        </router-link>
      </b-form-group>
    </form-login>
  </div>
</template>
<script>
import { validateGuest } from "../../mixins/auth";
import { mapActions } from "vuex";
export default {
  mixins: [validateGuest],
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
      this.$store.dispatch("login", this.params);
    },
  },
};
</script>
