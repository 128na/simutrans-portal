<template>
  <b-form>
    <b-form-group label="Email">
      <b-form-input type="email" v-model="email" autocomplete="email" />
    </b-form-group>
    <b-form-group label="Password">
      <form-password v-model="password" autocomplete="new-password" />
    </b-form-group>
    <b-form-group>
      <b-form-checkbox v-model="remember">Remember Me</b-form-checkbox>
    </b-form-group>
    <b-form-group>
      <b-button variant="primary" :disabled="fetching" @click="handleLogin">Login</b-button>
    </b-form-group>
  </b-form>
</template>
<script>
import { api_handlable } from "../../mixins";
export default {
  mixins: [api_handlable],
  props: ["user"],
  data() {
    return {
      email: "",
      password: "",
      remember: false
    };
  },
  created() {
    if (this.user) {
      this.$router.push({ name: "index" });
    }
  },
  methods: {
    handleLogin() {
      this.login({
        email: this.email,
        password: this.password,
        remember: this.remember
      });
    },
    setUser(user) {
      this.$emit("loggedin", user);
    }
  }
};
</script>
