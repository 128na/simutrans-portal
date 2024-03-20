<template>
  <dt>関連リンク</dt>
  <dd>
    <template v-for="(l, i) in links" :key="i">
      <div>
        <a :href="l" target="_blank" rel="noreferrer noopener">{{ l }}</a>
      </div>
    </template>
  </dd>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'RelatedLinks',
  components: {
  },
  props: {
    links: {
      type: Array,
      required: true,
    },
  },
  setup() {
  },
});
</script>
