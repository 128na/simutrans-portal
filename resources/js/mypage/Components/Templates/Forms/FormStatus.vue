<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-required />
        公開状態
      </template>
      <b-form-radio-group
        v-model="article.status"
        :options="options.statuses"
        button-variant="outline-primary"
        buttons
      ></b-form-radio-group>
      <validation-message field="article.status" />
    </b-form-group>
  </div>
</template>
<script>
import { mapGetters } from 'vuex';
export default {
  props: ['article'],
  computed: {
    ...mapGetters(['options', 'validationState'])
  }
};
</script>
