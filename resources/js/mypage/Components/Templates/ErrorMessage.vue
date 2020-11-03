<template>
  <div class="alert alert-danger" v-show="errorMessage">
    [{{ statusCode }}] {{ errorMessage }}
  </div>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  computed: {
    ...mapGetters(["errorMessage", "statusCode"]),
  },
};
</script>
