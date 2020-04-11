<template>
  <b-alert show variant="danger">
    <strong>{{$t('Verify Your Email Address')}}</strong>
    <p>{{$t('Please click the button below to verify your email address.')}}</p>
    <a href="#" class="alert-link" @click.prevent="handleResend">{{$t('Verify Email Address')}}</a>
  </b-alert>
</template>
<script>
import { api_handlable } from "../../mixins";
export default {
  props: ["user"],
  mixins: [api_handlable],
  methods: {
    handleResend() {
      this.resend();
      this.toastSuccess(
        "A fresh verification link has been sent to your email address."
      );
    }
  }
};
</script>
