<template>
  <div class="alert alert-success" v-show="infoMessage">
    {{ infoMessage }}
    <button type="button" class="close" @click="close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
export default {
  computed: {
    ...mapGetters(["infoMessage"]),
  },
  methods: {
    ...mapActions(["setInfoMessage"]),
    close() {
      this.$store.dispatch("setInfoMessage", {});
    },
  },
};
</script>
