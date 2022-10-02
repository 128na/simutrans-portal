<template>
  <router-view :menuOpen=menuOpen @toggleMenu="toggleMenu" />
</template>
<script>
import { defineComponent, ref } from 'vue';

export default defineComponent({
  name: 'App',

  setup() {
    const menuOpen = ref(false);

    return {
      menuOpen,
      toggleMenu() {
        menuOpen.value = !menuOpen.value;
      },
    };
  },
});
</script>
<style lang="scss">
* {
  transform: ease;
  color: 1s;
  background: 1s;
}
</style>
