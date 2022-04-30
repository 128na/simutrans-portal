<template>
  <div v-if="initialized">
    <global-menu />
    <main class="container-fluid bg-light py-4">
      <router-view />
    </main>
  </div>
  <loading-message v-else />
</template>
<script>
import { mapGetters, mapActions } from 'vuex';
export default {
  created() {
    this.initialize();
  },
  methods: {
    ...mapActions(['initialize'])
  },
  computed: {
    ...mapGetters(['initialized'])
  }
};
</script>
<style lang="scss">
#global-menu {
  .nav-icon {
    margin-bottom: 2px;
  }
  .navbar-text,
  .nav-link {
    color: rgba(255, 255, 255, 1);
    margin: 0 -0.9rem;
    padding: 0.5rem 1.4rem;
  }
  .nav-link {
    background-color: rgba(0, 0, 0, 0);
    &:hover {
      background-color: rgba(0, 0, 0, 0.1);
    }
    &.active {
      background-color: rgba(0, 0, 0, 0.2);
    }
  }
}
</style>
