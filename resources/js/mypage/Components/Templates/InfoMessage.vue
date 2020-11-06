<template>
  <b-alert variant="success" :show="!!infoMessage" dismissible>
    {{ infoMessage }}
  </b-alert>
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
      this.setInfoMessage({});
    },
  },
};
</script>
