<template>
  <q-item v-if="pr" :to="{ name: 'show', params: { idOrNickname: pr.user.nickname || pr.user.id, slug: pr.slug } }">
    <q-item-section>
      [PR] {{ pr.title }}
    </q-item-section>
  </q-item>
  <q-separator />
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'PrArticle',
  components: {
  },
  props: {
    pr: {
      type: Object,
      default: null,
    },
  },
});
</script>
