import { defineStore } from 'pinia';
import { useQuasar } from 'quasar';
import { computed, ref } from 'vue';

const listModes = {
  gallery: { icon: 'image', next: 'list', tooltip: 'ギャラリー' },
  list: { icon: 'list', next: 'show', tooltip: 'リスト' },
  show: { icon: 'subject', next: 'gallery', tooltip: '詳細' },
};
export const useListModeStore = defineStore('listMode', () => {
  const $q = useQuasar();
  const current = ref($q.localStorage.getItem('front.listMode') || 'list');

  const listMode = computed(() => listModes[current.value]);
  const nextMode = () => {
    current.value = listMode.value.next;
    $q.localStorage.set('front.listMode', listMode.value.next);
  };
  const is = (mode) => current.value === mode;

  return {
    listMode,
    nextMode,
    is,
  };
});
