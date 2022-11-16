<template>
  <q-card class="w-100">
    <q-item>
      <q-item-section avatar>
        <q-icon color="accent" name="sell" size="md" />
      </q-item-section>
      <q-item-section>
        <q-item-label>
          <p>{{ description.tag.name }}の説明</p>
          <text-pre>
            {{ description.tag.description || '説明がありません' }}
          </text-pre>
        </q-item-label>
        <q-item-label caption>
          作成: {{ description.tag.createdBy || '-' }} at {{ description.tag.createdAt }}<br />
          最終更新: {{ description.tag.lastModifiedBy || '-' }} at {{ description.tag.lastModifiedAt }}
        </q-item-label>
      </q-item-section>
    </q-item>
    <q-card-actions>
      <tag-editor :tag="description.tag" />
    </q-card-actions>
  </q-card>
</template>
<script>
import { defineComponent } from 'vue';
import TextPre from 'src/components/Common/Text/TextPre.vue';
import TagEditor from '../TagEditor.vue';

export default defineComponent({
  name: 'DescriptionTag',
  props: {
    description: {
      type: Object,
      required: true,
    },
  },
  components: {
    TextPre,
    TagEditor,
  },
  setup() {
    return {
    };
  },
});
</script>
