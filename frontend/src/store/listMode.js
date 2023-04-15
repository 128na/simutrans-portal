import { defineStore } from 'pinia';
import { useQuasar } from 'quasar';
import { computed, ref } from 'vue';

export const useListModeStore = defineStore('listMode', () => {
  const $q = useQuasar();
  const current = ref($q.localStorage.getItem('front.listMode') || 'list');

  const currentMode = computed({
    get() {
      return current.value;
    },
    set(v) {
      $q.localStorage.set('front.listMode', v);
      current.value = v;
    },
  });
  const is = (mode) => currentMode.value === mode;

  return {
    currentMode,
    is,
  };
});
