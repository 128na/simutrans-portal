<template>
  <span>
    <q-btn v-for="t in article.tags" :key="t.id" dense color="accent" size="sm" no-caps class="q-mr-xs q-mb-xs q-px-sm"
      @click.prevent="handle(t)">
      {{ t.name }}
    </q-btn>
  </span>
</template>

<script>
import { defineComponent } from 'vue';
import { useRouter } from 'vue-router';

export default defineComponent({
  name: 'TagList',
  props: {
    article: {
      type: Object,
      required: true,
    },
  },

  setup() {
    const router = useRouter();
    const handle = (t) => router.push({ name: 'tag', params: { id: t.id } });
    return {
      handle,
    };
  },
});
</script>
