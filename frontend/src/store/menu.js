import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useMenuStore = defineStore('menu', () => {
  const open = ref(true);
  const toggle = () => {
    open.value = !open.value;
  };

  return {
    open,
    toggle,
  };
});
