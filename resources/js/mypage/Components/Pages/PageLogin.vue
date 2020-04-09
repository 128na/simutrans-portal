<template>
  <div>
    <h1>{{$t('Login')}}</h1>
    <b-form>
      <form-login :params="params" :errors="errors" />
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
      params: {
        email: "",
        password: "",
        remember: false
      }
    };
  },
  created() {
    if (this.user) {
      this.$router.push({ name: "index" });
    }
  },
  methods: {
    handleLogin() {
      this.login(this.params);
    },
    setUser(user) {
      this.$emit("loggedin", user);
    }
  }
};
</script>
