import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useMenu = defineStore('menu', () => {
  const open = ref(true);
  const toggle = () => {
    open.value = !open.value;
  };

  return {
    open,
    toggle,
  };
});
