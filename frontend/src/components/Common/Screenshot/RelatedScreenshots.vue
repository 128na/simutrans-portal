<template>
  <dt>
    <slot>関連付けられたスクリーンショット</slot>
  </dt>
  <dd>
    <template v-for="s in screenshots" :key="s.id">
      <div>
        <a :href="`/screenshots/${s.id}`" target="_blank" class="text-primary" rel="noreferrer noopener">{{ s.title
          }}</a>
      </div>
    </template>
  </dd>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'RelatedScreenshots',
  components: {
  },
  props: {
    screenshots: {
      type: Array,
      required: true,
    },
  },
  setup() {
  },
});
</script>
