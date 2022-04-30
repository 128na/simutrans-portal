<template>
  <transition
    name="fade"
    mode="out-in"
  >
    <div v-if="initialized">
      <global-menu />
      <main class="container-fluid bg-light py-4">
        <error-message />
        <info-message />
        <transition
          name="fade"
          mode="out-in"
        >
          <router-view />
        </transition>
      </main>
    </div>
    <loading-message v-else />
  </transition>
</template>
<script>
import { mapGetters, mapActions } from 'vuex';
export default {
  created() {
    this.checkLogin();
  },
  methods: {
    ...mapActions(['checkLogin'])
  },
  computed: {
    ...mapGetters(['initialized'])
  }
};
</script>
<style lang="scss">
.fade-enter-active {
  transition: opacity 0.05s;
}
.fade-leave-active {
  transition: opacity 0.1s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}
/* 汎用 */
.clickable {
  cursor: pointer;
}
:disabled {
  cursor: busy;
}
.pre-line {
  white-space: pre-line;
}
/* アイコンの縦位置調整 */
a.dropdown-item,
a.btn,
button.btn {
  display: inline-flex;
  align-items: center;
}

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
    &:hover {
      background-color: rgba(0, 0, 0, 0.1);
    }
    &.active {
      background-color: rgba(0, 0, 0, 0.2);
    }
  }
}
</style>
