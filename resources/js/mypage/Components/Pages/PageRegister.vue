<template>
  <div>
    <button-back name="login">{{$t('Back to Login')}}</button-back>
    <h1>{{$t('Register')}}</h1>
    <b-form>
      <b-form-group :label="$t('Name')">
        <b-form-input type="text" v-model="name" autocomplete="name" />
      </b-form-group>
      <b-form-group :label="$t('Email')">
        <b-form-input type="email" v-model="email" autocomplete="email" />
      </b-form-group>
      <b-form-group :label="$t('Password')">
        <form-password v-model="password" autocomplete="new-password" />
      </b-form-group>
      <b-form-group>
        <b-button variant="primary" :disabled="fetching" @click="handleRegister">{{$t('Register')}}</b-button>
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
      name: "",
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
        name: this.name,
        email: this.email,
        password: this.password
      });
    },
    setUser(user) {
      this.$emit("loggedin", user);
    }
  }
};
</script>
