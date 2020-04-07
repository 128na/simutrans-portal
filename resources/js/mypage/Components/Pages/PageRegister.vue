<template>
  <b-form>
    <b-form-group label="Username">
      <b-form-input type="text" v-model="username" autocomplete="username" />
    </b-form-group>
    <b-form-group label="Email">
      <b-form-input type="email" v-model="email" autocomplete="email" />
    </b-form-group>
    <b-form-group label="Password">
      <form-password v-model="password" autocomplete="new-password" />
    </b-form-group>
    <b-form-group>
      <b-button variant="primary" :disabled="fetching" @click="handleRegister">Register</b-button>
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
      username: "",
      email: "",
      password: ""
    };
  },
  created() {
    if (this.user) {
      this.$router.push({ name: "index" });
    }
  },
  methods: {
    handleRegister() {
      this.register({
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
