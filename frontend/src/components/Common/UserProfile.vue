<template>
  <q-card class="w-100">
    <q-item>
      <q-item-section avatar>
        <q-avatar>
          <img :src="profile.avatar_url">
        </q-avatar>
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ profile.name }}</q-item-label>
      </q-item-section>
    </q-item>

    <q-card-section>
      <text-pre>
        {{ profile.description || '説明がありません' }}
      </text-pre>
    </q-card-section>
    <template v-if="profile.twitter || profile.website">
      <q-separator />
      <q-card-actions>
        <q-btn flat color="primary" v-if="profile.twitter" :href="`https://twitter.com/${profile.twitter}`"
          rel="noopener noreferrer" no-caps label="Twitter"></q-btn>
        <q-btn flat color="primary" v-if="profile.website" :href="profile.website" rel="noopener noreferrer" no-caps
          label="Website"></q-btn>
      </q-card-actions>
    </template>
  </q-card>
</template>
<script>
import { defineComponent } from 'vue';
import TextPre from 'src/components/Common/TextPre.vue';

export default defineComponent({
  name: 'UserProfile',
  props: {
    profile: {
      type: Object,
      required: true,
    },
  },
  components: {
    TextPre,
  },
});
</script>
