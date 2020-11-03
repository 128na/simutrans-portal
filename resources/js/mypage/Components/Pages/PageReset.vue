<template>
  <div>
    <button-back name="login">{{ $t("Back to Login") }}</button-back>
    <h1>{{ $t("Reset Password") }}</h1>
    <form-reset :params="params">
      <b-form-group>
        <fetching-overlay>
          <b-button variant="primary" type="submit" @click="handleSubmit">
            {{ $t("Send Password Reset Link") }}
          </b-button>
        </fetching-overlay>
      </b-form-group>
    </form-reset>
  </div>
</template>
<script>
import { validateGuest } from "../../mixins/auth";
import { mapActions } from "vuex";
export default {
  mixins: [validateGuest],
  data() {
    return {
      params: {
        email: "",
      },
    };
  },
  methods: {
    ...mapActions(["sendResetEmail"]),
    handleSubmit() {
      this.$store.dispatch("sendResetEmail", this.params);
    },
  },
};
</script>
