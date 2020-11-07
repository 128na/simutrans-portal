<template>
  <div>
    <page-title>PHP info</page-title>
    <div v-html="phpinfo" />
  </div>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
export default {
  created() {
    if (!this.phpinfo) {
      this.fetchPhpinfo();
    }
  },
  computed: {
    ...mapGetters(["phpinfo"]),
  },
  methods: {
    ...mapActions(["fetchPhpinfo"]),
  },
};
</script>
