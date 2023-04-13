import { defineStore } from 'pinia';
import { useQuasar } from 'quasar';
import { ref } from 'vue';

export const useMenuStore = defineStore('menu', () => {
  const $q = useQuasar();
  const { desktop } = $q.platform.is;
  const open = ref(desktop);
  const toggle = () => {
    open.value = !open.value;
  };

  return {
    open,
    toggle,
  };
});

export const useMenuRightStore = defineStore('menuRight', () => {
  const $q = useQuasar();
  const { desktop } = $q.platform.is;
  const open = ref(desktop);
  const toggle = () => {
    open.value = !open.value;
  };

  return {
    open,
    toggle,
  };
});
