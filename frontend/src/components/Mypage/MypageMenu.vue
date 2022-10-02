<template>
  <q-list>
    <q-item clickable :to="{name:'top'}">
      <q-item-section>フロントページ</q-item-section>
    </q-item>
    <q-item clickable :to="{name:'admin'}">
      <q-item-section>管理画面</q-item-section>
    </q-item>
    <q-separator />
    <MetaInfo />
  </q-list>

</template>

<script>
import { defineComponent } from 'vue';
import MetaInfo from '../MetaInfo.vue';

export default defineComponent({
  name: 'MypageMenu',

  components: {
    MetaInfo,
  },

  setup() {
    return {
    };
  },
});
</script>
