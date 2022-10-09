<template>
  <q-input :model-value="modelValue" :type="show ? 'text' :'password'" @change="$emit('update:modelValue', $event)">
    <template v-slot:append>
      <q-icon :name="show ? 'visibility' : 'visibility_off'" @click="show=!show" />
    </template>
  </q-input>
</template>

<script>
import { defineComponent, ref } from 'vue';

export default defineComponent({
  name: 'InputPassword',
  props: {
    modelValue: {
      type: String,
      default: '',
    },
  },
  setup() {
    const show = ref(false);

    return { show };
  },
});
</script>
