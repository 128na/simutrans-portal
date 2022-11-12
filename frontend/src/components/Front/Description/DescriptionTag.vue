<template>
  <q-card class="w-100">
    <q-item>
      <q-item-section avatar>
        <q-icon color="accent" name="sell" size="md" />
      </q-item-section>
      <q-item-section>
        <q-item-label>
          <p>{{ description.name }}の説明</p>
          <text-pre>
            {{ description.message || '説明がありません' }}
          </text-pre>
        </q-item-label>
        <q-item-label caption>
          作成: {{ description.createdBy || '-' }} at {{ description.createdAt }}<br />
          最終更新: {{ description.lastModifiedBy || '-' }} at {{ description.updatedAt }}
        </q-item-label>
      </q-item-section>
    </q-item>
    <q-card-actions v-if="auth.isLoggedIn">
      <q-btn flat color="primary" v-if="description.editable" label="編集する" />
      <q-btn flat color="primary" v-else label="編集できません" disabled />
    </q-card-actions>
  </q-card>
</template>
<script>
import { defineComponent } from 'vue';
import TextPre from 'src/components/Common/Text/TextPre.vue';
import { useAuthStore } from 'src/store/auth';

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
  },
  setup() {
    const auth = useAuthStore();

    return {
      auth,
    };
  },
});
</script>
