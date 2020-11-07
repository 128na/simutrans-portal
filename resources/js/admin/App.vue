<template>
  <div v-if="initialized">
    <main class="bg-light d-flex h-100">
      <div id="menu" class="border-right p-4 pt-5">
        <page-title>管理機能</page-title>
        <global-menu />
      </div>
      <div id="main">
        <router-view class="p-4 pt-5" />
      </div>
    </main>
  </div>
  <loading v-else />
</template>
<script>
import { mapGetters, mapActions } from "vuex";
export default {
  created() {
    this.initialize();
  },
  methods: {
    ...mapActions(["initialize"]),
  },
  computed: {
    ...mapGetters(["initialized"]),
  },
};
</script>
<style lang="scss" scoped>
#menu {
  width: 200px;
  min-height: 100vh;
}
#main {
  flex: 1;
}
</style>
