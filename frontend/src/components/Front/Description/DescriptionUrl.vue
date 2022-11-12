<template>
  <q-card class="w-100">
    <q-card-section>
      詳細情報<br /><a :href="description.url" target="_blank" rel="noopener nofollow">{{ description.url }}</a>
    </q-card-section>
  </q-card>
</template>
<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'Description',
  props: {
    description: {
      type: Object,
      required: true,
    },
  },
  components: {
  },
});
</script>
