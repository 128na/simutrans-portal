<template>
  <q-card class="w-100">
    <q-card-section>
      <text-pre>
        {{ description.message || '説明がありません' }}
      </text-pre>
    </q-card-section>
  </q-card>
</template>
<script>
import { defineComponent } from 'vue';
import TextPre from 'src/components/Common/Text/TextPre.vue';

export default defineComponent({
  name: 'DescriptionMessage',
  props: {
    description: {
      type: Object,
      required: true,
    },
  },
  components: {
    TextPre,
  },
});
</script>
