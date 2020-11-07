<template>
  <div>
    <page-title>PHP info</page-title>
    <iframe :srcdoc="phpinfo" class="w-100" sandbox />
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
<style lang="scss" scoped>
iframe {
  min-height: 85vh;
}
</style>
