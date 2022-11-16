<template>
  <q-card class="w-100">
    <q-card-section v-if="description.url">
      詳細情報<br /><a :href="description.url" target="_blank" rel="noopener nofollow">{{ description.url }}</a>
    </q-card-section>
    <q-card-section v-else>
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
