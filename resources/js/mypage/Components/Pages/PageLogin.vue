<template>
  <div>
    <h1>Login</h1>
    <b-form>
      <b-form-group label="Email">
        <b-form-input type="email" v-model="email" autocomplete="email" />
      </b-form-group>
      <b-form-group label="Password">
        <form-password v-model="password" autocomplete="current-password" />
      </b-form-group>
      <b-form-group>
        <b-form-checkbox v-model="remember">Remember Me</b-form-checkbox>
      </b-form-group>
      <b-form-group>
        <b-button class="mr-1" variant="primary" :disabled="fetching" @click="handleLogin">Login</b-button>
        <router-link :to="to_register">Register</router-link>&nbsp;
        <router-link :to="to_reset">Forget Password</router-link>
      </b-form-group>
    </b-form>
  </div>
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
  computed: {
    to_register() {
      return { name: "register" };
    },
    to_reset() {
      return { name: "reset" };
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
