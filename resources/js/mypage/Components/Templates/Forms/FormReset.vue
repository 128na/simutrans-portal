<template>
  <b-form>
    <b-form-group label="メールアドレス">
      <b-form-input
        v-model="params.email"
        type="email"
        autocomplete="email"
        :state="validationState('email')"
      />
      <validation-message field="email" />
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
