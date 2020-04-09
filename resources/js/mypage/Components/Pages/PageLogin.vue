<template>
  <div>
    <h1>{{$t('Login')}}</h1>
    <b-form>
      <b-form-group :label="$t('Email')">
        <b-form-input type="email" v-model="email" autocomplete="email" />
      </b-form-group>
      <b-form-group :label="$t('Password')">
        <form-password v-model="password" autocomplete="current-password" />
      </b-form-group>
      <b-form-group>
        <b-form-checkbox v-model="remember">{{$t('Remember Me')}}</b-form-checkbox>
      </b-form-group>
      <b-form-group>
        <b-button
          class="mr-1"
          variant="primary"
          :disabled="fetching"
          @click="handleLogin"
        >{{$t('Login')}}</b-button>
        <router-link :to="to_register">{{$t('Register')}}</router-link>&nbsp;|&nbsp;
        <router-link :to="to_reset">{{$t('Forgot Your Password?')}}</router-link>
      </b-form-group>
    </b-form>
  </div>
</template>
<script>
import { api_handlable, linkable } from "../../mixins";
export default {
  mixins: [api_handlable, linkable],
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
