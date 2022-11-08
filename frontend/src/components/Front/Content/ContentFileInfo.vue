<template>
  <q-expansion-item v-if="dats" label="datファイル一覧" switch-toggle-side>
    <q-item v-for="(names, filename) in dats" :key="filename">
      <q-item-section>
        <q-item-label>{{ filename }}</q-item-label>
        <q-item-label caption>
          <div v-for="(n, index) in names" :key="`${n}-${index}`">
            {{ n }}
          </div>
        </q-item-label>
      </q-item-section>
    </q-item>
  </q-expansion-item>
  <q-expansion-item v-if="tabs" label="tabファイル一覧" switch-toggle-side>
    <q-item v-for="(names, filename) in tabs" :key="filename">
      <q-item-section>
        <q-item-label>{{ filename }}</q-item-label>
        <q-item-label caption>
          <div v-for="(translated, original) in names" :key="original">
            {{ translated }} ({{ original }})
          </div>
        </q-item-label>
      </q-item-section>
    </q-item>
  </q-expansion-item>
</template>
<script>
import { defineComponent, computed } from 'vue';

export default defineComponent({
  name: 'ContentFileInfo',
  props: {
    article: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const fileInfo = computed(() => {
      if (props.article.contents.file) {
        const attachmentId = parseInt(props.article.contents.file, 10);
        return props.article.attachments.find((a) => a.id === attachmentId)?.fileInfo;
      }
      return null;
    });
    const dats = computed(() => {
      if (fileInfo.value) {
        return fileInfo.value.dats;
      }
      return null;
    });
    const tabs = computed(() => {
      if (fileInfo.value) {
        return fileInfo.value.tabs;
      }
      return null;
    });

    return {
      dats,
      tabs,
    };
  },
});
</script>
