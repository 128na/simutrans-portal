<template>
  <q-list>

    <q-item dense clickable href="https://twitter.com/128Na" target="_blank" rel="noopener nofollow">
      @128Na
    </q-item>
    <q-item dense clickable href="https://github.com/128na/simutrans-portal" target="_blank" rel="noopener nofollow">
      Github
    </q-item>
    <q-item dense clickable href="/feed" target="_blank">
      <q-icon name="rss_feed" />Atom
    </q-item>
  </q-list>
</template>
<script>
import { defineComponent, ref } from 'vue';
import { staticUrls } from '../../composables/routeShortcut';

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
