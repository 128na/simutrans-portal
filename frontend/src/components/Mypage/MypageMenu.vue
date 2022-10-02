<template>
  <q-list>
    <q-item :to="{name:'top'}">フロントページ</q-item>
    <q-item :to="{name:'admin'}">管理画面</q-item>
  </q-list>

</template>

<script>
import { defineComponent } from 'vue';
// import { routeTo } from '../../composables/routeShortcut';

export default defineComponent({
  name: 'MypageMenu',

  components: {
  },

  setup() {
    return {
    };
  },
});
</script>
