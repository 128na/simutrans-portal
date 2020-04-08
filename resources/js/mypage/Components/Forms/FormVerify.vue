<template>
  <b-alert show variant="danger">
    Need Verify
    <a href="#" class="alert-link" @click.prevent="handleResend">Resend Email</a>
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
      this.toastSuccess("Mail Resend");
    }
  }
};
</script>
