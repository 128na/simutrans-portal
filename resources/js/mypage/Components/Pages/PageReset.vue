<template>
  <div>
    <h1>パスワード再設定</h1>
    <form-reset :params="params">
      <b-form-group>
        <fetching-overlay>
          <b-button variant="primary" type="submit" @click="handleSubmit">
            再設定用のメールを送信
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
      this.sendResetEmail(this.params);
    },
  },
};
</script>
