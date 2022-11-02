<template>
  <span>
    <q-btn v-for="c in article.categories" :key="key(c)" dense color="secondary" size="sm" no-caps
      class="q-mr-xs q-mb-xs q-px-sm" @click.prevent="handle(c)">
      {{ c.name }}
    </q-btn>
  </span>
</template>

<script>
import { defineComponent } from 'vue';
import { useRouter } from 'vue-router';

const key = (category) => `${category.type}-${category.slug}`;

export default defineComponent({
  name: 'CategoryList',
  props: {
    article: {
      type: Object,
      required: true,
    },
  },

  setup() {
    const router = useRouter();
    const handle = (c) => router.push({ name: 'category', params: { type: c.type, slug: c.slug } });
    return {
      key,
      handle,
    };
  },
});
</script>
