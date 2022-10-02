<template>
  <q-list>
    <q-item :to="{name:'top'}">フロントページ</q-item>
    <q-item :to="{name:'mypage'}">マイページ</q-item>
  </q-list>

</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'AdminMenu',

  components: {
  },

  setup() {
    return {
    };
  },
});
</script>
