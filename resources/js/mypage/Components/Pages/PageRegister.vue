<template>
  <div>
    <button-back name="login">{{ $t("Back to Login") }}</button-back>
    <h1>{{ $t("Register") }}</h1>
    <form-register :params="params">
      <b-form-group>
        <fetching-overlay>
          <b-button variant="primary" type="submit" @click="handleRegister">
            {{ $t("Register") }}
          </b-button>
        </fetching-overlay>
      </b-form-group>
    </form-register>
  </div>
</template>
<script>
import { validateGuest } from "../../mixins/auth";
import { mapActions } from "vuex";
export default {
  mixins: [validateGuest],
  props: ["user"],
  data() {
    return {
      params: {
        name: "",
        email: "",
        password: "",
      },
    };
  },
  methods: {
    ...mapActions(["register"]),
    handleRegister() {
      this.$store.dispatch("register", this.params);
    },
  },
};
</script>
