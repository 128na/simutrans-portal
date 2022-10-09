import { defineStore } from 'pinia';
import { setCssVar } from 'quasar';

export const useColorStore = defineStore('color', () => {
  const setFront = () => {
    setCssVar('primary', 'hsl(211, 82%, 54%)');
  };
  const setMypage = () => {
    setCssVar('primary', 'hsl(132, 82%, 31%)');
  };
  const setAdmin = () => {
    setCssVar('primary', 'hsl(345, 82%, 35%)');
  };

  return {
    setFront,
    setMypage,
    setAdmin,
  };
});
