<template>
  <q-list>
    <q-item dense>
      <q-item-section>
        {{ appName}} v {{appVersion}}
      </q-item-section>
    </q-item>
  </q-list>
</template>
<script>
import { defineComponent } from 'vue';
import { useAppInfo } from 'src/composables/appInfo';

export default defineComponent({
  name: 'MetaLinks',
  setup() {
    const { appName, appVersion } = useAppInfo();
    return {
      appName,
      appVersion,
    };
  },
});
</script>
