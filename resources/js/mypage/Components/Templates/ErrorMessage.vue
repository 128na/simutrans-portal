<template>
  <b-alert variant="danger" :show="!!errorMessage">
    [{{ statusCode }}] {{ errorMessage }}
  </b-alert>
</template>
<script>
import { mapGetters } from 'vuex';
export default {
  computed: {
    ...mapGetters(['errorMessage', 'statusCode'])
  }
};
</script>
