<template>
  <div>
    <page-title>新規登録</page-title>
    <form-register :params="params">
      <b-form-group>
        <b-button
          variant="primary"
          type="submit"
          @click.prevent="handleRegister"
        >
          新規登録
        </b-button>
      </b-form-group>
    </form-register>
  </div>
</template>
<script>
import { mapActions } from "vuex";
export default {
  data() {
    return {
      params: {
        name: "",
        email: "",
      },
    };
  },
  methods: {
    ...mapActions(["storeUser"]),
    async handleRegister() {
      const res = await this.storeUser(this.params);
      if (res) {
        params.name = "";
        params.email = "";
      }
      alert(res ? "成功" : "失敗");
    },
  },
};
</script>
