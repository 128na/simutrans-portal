<template>
  <q-list>
    <q-item clickable :to="{name:'top'}">
      <q-item-section>トップページ</q-item-section>
    </q-item>
    <q-item clickable :to="{name:'mypage'}">
      <q-item-section>マイページ</q-item-section>
    </q-item>
    <q-separator />
    <MetaInfo />
  </q-list>

</template>

<script>
import { defineComponent } from 'vue';
import MetaInfo from '../MetaInfo.vue';

export default defineComponent({
  name: 'AdminMenu',

  components: {
    MetaInfo,
  },

  setup() {
    return {
    };
  },
});
</script>
