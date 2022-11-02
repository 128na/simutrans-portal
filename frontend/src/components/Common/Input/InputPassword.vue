<template>
  <q-input :model-value="modelValue" :type="show ? 'text' : 'password'"
    @update:modelValue="$emit('update:modelValue', $event)" @blur="show = false">
    <template v-slot:append>
      <q-icon :name="show ? 'visibility' : 'visibility_off'" class="cursor-pointer" @click="show = !show" />
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
