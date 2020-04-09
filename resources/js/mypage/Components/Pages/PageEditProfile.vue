<template>
  <div>
    <button-back />
    <h1>{{$t('Edit my profile')}}</h1>
    <form-profile
      :user="user"
      :attachments="attachments"
      @update:attachments="$emit('update:attachments', $event)"
      @update:user="$emit('update:user', $event)"
    />
    <b-form-group>
      <b-btn :disabled="fetching" variant="primary" @click="handleUpdate">{{$t('Save')}}</b-btn>
    </b-form-group>
  </div>
</template>
<script>
import { toastable, verifiedable, api_handlable } from "../../mixins";
export default {
  props: ["user", "attachments"],
  mixins: [toastable, verifiedable, api_handlable],
  methods: {
    handleUpdate() {
      this.updateUser(this.user);
    },
    setUser(user) {
      this.$emit("update:user", user);
      this.$router.push({ name: "index" });
    }
  }
};
</script>
