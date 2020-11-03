<template>
  <transition name="fade" mode="out-in">
    <div v-if="initialized">
      <header-menu />
      <main class="container-fluid bg-light py-4">
        <error-message />
        <info-message />
        <transition name="fade" mode="out-in">
          <router-view />
        </transition>
      </main>
    </div>
    <loading v-else />
  </transition>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
export default {
  created() {
    this.checkLogin();
  },
  methods: {
    ...mapActions(["checkLogin"]),
  },
  computed: {
    ...mapGetters(["initialized"]),
  },
};
</script>
<style>
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
</style>
