<template>
  <q-list>

    <q-item dense clickable href="https://twitter.com/PortalSimutrans" target="_blank" rel="noopener nofollow">
      <q-item-section>@PortalSimutrans</q-item-section>
    </q-item>
    <q-item dense clickable href="https://twitter.com/128Na" target="_blank" rel="noopener nofollow">
      <q-item-section>@128Na</q-item-section>
    </q-item>
    <q-item dense clickable href="https://github.com/128na/simutrans-portal" target="_blank" rel="noopener nofollow">
      <q-item-section>Github</q-item-section>
    </q-item>
    <q-item dense clickable :href="feedUrl" target="_blank">
      <q-item-section>
        RSS (Atom)
      </q-item-section>
    </q-item>
  </q-list>
</template>
<script>
import { defineComponent } from 'vue';
import { staticUrls } from 'src/composables/staticUrls';

export default defineComponent({
  name: 'MetaLinks',
  setup() {
    const { feedUrl } = staticUrls();
    return {
      feedUrl,
    };
  },
});
</script>
