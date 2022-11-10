<template>
  <transition mode="out-in">
    <div v-if="isDark">
      <q-icon name="dark_mode" size="sm" color="warning" class="cursor-pointer night toggle-day-night" @click="handle"
        data-cy="btn-dark" />
    </div>
    <div v-else>
      <q-icon name="light_mode" size="sm" color="white" class="cursor-pointer toggle-day-night" @click="handle"
        data-cy="btn-light" />
    </div>
  </transition>
</template>
<script>

import { defineComponent, computed } from 'vue';
import { useQuasar } from 'quasar';

export default defineComponent({
  name: 'ToggleDarkMode',
  setup() {
    const $q = useQuasar();
    $q.dark.set($q.localStorage.getItem('darkmode') === 'darkmode');
    return {
      handle() {
        $q.dark.toggle();
        $q.localStorage.set('darkmode', $q.dark.isActive ? 'darkmode' : '');
      },
      isDark: computed(() => $q.dark.isActive),
    };
  },
});
</script>
<style scoped lang="scss">
.night {
  text-shadow: $dark 1px 0 10px;
}

.toggle-day-night {
  transition: all ease 4s;
  transform-origin: center 100px;
}
</style>
