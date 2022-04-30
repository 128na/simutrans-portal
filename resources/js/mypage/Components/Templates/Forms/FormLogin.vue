<template>
  <b-form>
    <b-form-group label="メールアドレス">
      <b-form-input
        v-model="params.email"
        type="email"
        :state="validationState('email', 'password')"
        autocomplete="email"
      />
    </b-form-group>
    <b-form-group label="パスワード">
      <input-password
        v-model="params.password"
        :state="validationState('email', 'password')"
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
import { mapGetters } from 'vuex';
export default {
  props: ['params'],
  computed: {
    ...mapGetters(['validationState'])
  }
};
</script>
