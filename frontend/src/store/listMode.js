import { defineStore } from 'pinia';
import { useQuasar } from 'quasar';
import { computed, ref } from 'vue';

const listModes = [
  { value: 'gallery', icon: 'image', label: 'ギャラリー' },
  { value: 'list', icon: 'list', label: 'リスト' },
  { value: 'show', icon: 'subject', label: '詳細' },
];

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
    listModes,
    currentMode,
    is,
  };
});
