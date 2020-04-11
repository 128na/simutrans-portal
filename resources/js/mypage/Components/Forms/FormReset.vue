<template>
  <div>
    <b-form-group :label="$t('Email')">
      <b-form-input
        type="email"
        v-model="params.email"
        autocomplete="email"
        :state="state('email')"
      />
    </b-form-group>
  </div>
</template>
<script>
import { validatable } from "../../mixins";
export default {
  props: ["params"],
  mixins: [validatable]
};
</script>
