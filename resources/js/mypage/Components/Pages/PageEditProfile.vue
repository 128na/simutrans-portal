<template>
  <div>
    <button-back />
    <h1>{{ $t("Edit my profile") }}</h1>
    <form-profile
      :user="copy"
      :attachments="attachments"
      :errors="errors"
      @update:attachments="$emit('update:attachments', $event)"
      @update:user="$emit('update:user', $event)"
    />
    <b-form-group>
      <b-btn :disabled="fetching" variant="primary" @click="handleUpdate">{{
        $t("Save")
      }}</b-btn>
    </b-form-group>
  </div>
</template>
<script>
import {
  toastable,
  verifiedable,
  api_handlable,
  editor_handlable,
} from "../../mixins";
export default {
  props: ["user", "attachments"],
  mixins: [toastable, verifiedable, api_handlable, editor_handlable],
  created() {
    this.setCopy(this.user);
  },
  methods: {
    handleUpdate() {
      this.updateUser(this.copy);
    },
    async setUser(user) {
      this.$emit("update:user", user);
      this.setCopy(user);
      await this.$nextTick();
      this.$router.push({ name: "index" });
    },
    getOriginal() {
      return this.user;
    },
  },
};
</script>
