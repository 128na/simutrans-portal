<template>
  <div>
    <button-back name="login">{{$t('Back to Login')}}</button-back>
    <h1>{{$t('Register')}}</h1>
    <b-form>
      <form-register :params="params" :errors="errors" />
      <b-form-group>
        <b-button
          variant="primary"
          type="submit"
          :disabled="fetching"
          @click="handleRegister"
        >{{$t('Register')}}</b-button>
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
      params: {
        name: "",
        email: "",
        password: ""
      }
    };
  },
  created() {
    if (this.user) {
      this.$router.push({ name: "index" });
    }
  },
  methods: {
    handleRegister() {
      this.register(this.params);
    },
    setUser(user) {
      this.$emit("loggedin", user);
    }
  }
};
</script>
