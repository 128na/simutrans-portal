<template>
  <label-optional>関連リンク（10件まで）</label-optional>
  <template v-for="(_, index) in store.screenshot.links" :key="index">
    <q-input v-model="store.screenshot.links[index]" type="url" :label="`リンク ${index + 1}`" bottom-slots
      :error-message="store.vali(`screenshot.links.${index}`)" :error="!!store.vali(`screenshot.links.${index}`)">

      <template v-slot:after>
        <q-btn icon="close" round outline color="negative" size="xs" @click="remove(index)" />
      </template>
    </q-input>
  </template>
  <q-btn color="primary" outline label="リンクを追加" class="q-my-sm" @click="add" />
</template>
<script>
import LabelOptional from 'src/components/Common/LabelOptional.vue';
import { useScreenshotEditStore } from 'src/store/screenshotEdit';
import { defineComponent } from 'vue';

const MAX_LINKS = 10;
export default defineComponent({
  name: 'FormLinks',
  components: {
    LabelOptional,
  },
  setup() {
    const store = useScreenshotEditStore();
    const add = () => {
      if (store.screenshot.links.length < MAX_LINKS) {
        store.screenshot.links.push('');
      }
    };
    const remove = (index) => {
      if (window.confirm('リンクを削除しますか？')) {
        store.screenshot.links.splice(index, 1);
      }
    };
    return {
      store,
      add,
      remove,
    };
  },
});
</script>
