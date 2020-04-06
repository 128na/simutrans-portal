<template>
  <div>
    <router-link :to="{name:'index'}">Back to MyPage</router-link>
    <form-profile
      :user="user"
      :attachments="attachments"
      @update:user="$emit('update:user', $event)"
    />
    <b-form-group>
      <b-btn variant="primary" @click="handleUpdate">Update</b-btn>
    </b-form-group>
  </div>
</template>
<script>
import api from "../../api";
import { toastable, verifiedable } from "../../mixins";
export default {
  props: ["user", "attachments"],
  mixins: [toastable, verifiedable],
  methods: {
    handleUpdate() {
      this.update();
    },
    async update() {
      const res = await api.updateUser(this.user).catch(this.handleErrorToast);

      console.log(res);
      if (res && res.status === 200) {
        this.$emit("update:user", res.data.data);
        this.$router.push({ name: "index" });
      }
    }
  }
};
</script>
