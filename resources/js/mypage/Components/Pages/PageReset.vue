<template>
  <div>
    <button-back name="login">{{$t('Back to Login')}}</button-back>
    <h1>{{$t('Reset Password')}}</h1>
    <b-form>
      <b-form-group :label="$t('Email')">
        <b-form-input type="email" v-model="email" autocomplete="email" />
      </b-form-group>
      <b-form-group>
        <b-button
          variant="primary"
          :disabled="fetching"
          @click="handleSubmit"
        >{{$t('Send Password Reset Link')}}</b-button>
      </b-form-group>
    </b-form>
  </div>
</template>
<script>
import { api_handlable } from "../../mixins";
export default {
  mixins: [api_handlable],
  data() {
    return {
      email: ""
    };
  },
  methods: {
    handleSubmit() {
      this.reset({
        email: this.email
      });
    },
    sent() {
      this.toastSuccess(
        "You are receiving this email because we received a password reset request for your account."
      );
    }
  }
};
</script>
