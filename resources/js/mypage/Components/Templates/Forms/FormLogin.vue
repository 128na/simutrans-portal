<template>
  <b-form>
    <b-form-group label="メールアドレス">
      <b-form-input
        type="email"
        v-model="params.email"
        :state="validationState('password')"
        autocomplete="email"
      />
    </b-form-group>
    <b-form-group label="パスワード">
      <input-password
        v-model="params.password"
        :state="validationState('password')"
        autocomplete="current-password"
      />
    </b-form-group>
    <b-form-group>
      <b-form-checkbox v-model="params.remember">
        ログインしたままにする
      </b-form-checkbox>
    </b-form-group>
    <slot />
  </b-form>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  props: ["params"],
  computed: {
    ...mapGetters(["validationState"]),
  },
};
</script>
