<template>
  <div>
    <button-back name="login">Back to Login</button-back>
    <h1>Reset</h1>
    <b-form>
      <b-form-group label="Email">
        <b-form-input type="email" v-model="email" autocomplete="email" />
      </b-form-group>
      <b-form-group>
        <b-button variant="primary" :disabled="fetching" @click="handleSubmit">Submit</b-button>
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
      this.toastSuccess("Reset mail sent");
    }
  }
};
</script>
