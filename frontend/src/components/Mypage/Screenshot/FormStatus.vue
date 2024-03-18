<template>
  <label-required>公開状態</label-required>
  <q-btn-toggle v-model="store.screenshot.status" :options="options" name="status" />
</template>
<script>
import { defineComponent } from 'vue';
import LabelRequired from 'src/components/Common/LabelRequired.vue';
import { useScreenshotEditStore } from 'src/store/screenshotEdit';

const options = [
  { label: '公開', value: 'Publish' },
  { label: '非公開', value: 'Private' },
];
export default defineComponent({
  name: 'FormStatus',
  components: { LabelRequired },
  setup() {
    const store = useScreenshotEditStore();

    return {
      store,
      options,
    };
  },
});
</script>
