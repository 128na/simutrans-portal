<template>
  <div>
    <h1>新規登録</h1>
    <form-register :params="params">
      <b-form-group>
        <fetching-overlay>
          <b-button variant="primary" type="submit" @click="handleRegister">
            新規登録
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
      this.register(this.params);
    },
  },
};
</script>
