<template>
  <q-page>
    <text-title>example</text-title>
  </q-page>
</template>
<script>
import TextTitle from 'src/components/Common/TextTitle.vue';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'PageIndex',
  components: { TextTitle },
  setup() { },
});
</script>
