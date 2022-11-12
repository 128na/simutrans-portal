<template>
  <q-card class="w-100">
    <q-item>
      <q-item-section avatar>
        <q-avatar>
          <img :src="description.profile.avatar_url">
        </q-avatar>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ description.profile.name }}</q-item-label>
      </q-item-section>
    </q-item>

    <q-card-section>
      <text-pre>
        {{ description.profile.description || '説明がありません' }}
      </text-pre>
    </q-card-section>
    <template v-if="description.profile.twitter || description.profile.website">
      <q-separator />
      <q-card-actions>
        <q-btn flat color="primary" v-if="description.profile.twitter"
          :href="`https://twitter.com/${description.profile.twitter}`" rel="noopener noreferrer" target="_blank" no-caps
          label="Twitter"></q-btn>
        <q-btn flat color="primary" v-if="description.profile.website" :href="description.profile.website"
          rel="noopener noreferrer" target="_blank" no-caps label="Website"></q-btn>
      </q-card-actions>
    </template>
  </q-card>
</template>
<script>
import { defineComponent } from 'vue';
import TextPre from 'src/components/Common/Text/TextPre.vue';

export default defineComponent({
  name: 'DescriptionProfile',
  props: {
    description: {
      type: Object,
      required: true,
    },
  },
  components: {
    TextPre,
  },
});
</script>
