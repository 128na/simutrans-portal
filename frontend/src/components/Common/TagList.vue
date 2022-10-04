<template>
  <span>
    <q-btn v-for="t in article.tags" :key="t.id" dense color="grey-7" size="sm" no-caps class="q-mr-xs"
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
