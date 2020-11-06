<template></template>
<script>
import { validateLogin } from "../../mixins/auth";
import { mapGetters, mapActions } from "vuex";
export default {
  mixins: [validateLogin],
  created() {
    if (this.isLoggedIn) {
      this.logout();
    }
  },
  computed: {
    ...mapGetters(["isLoggedIn"]),
  },
  methods: {
    ...mapActions(["logout"]),
  },
};
</script>
