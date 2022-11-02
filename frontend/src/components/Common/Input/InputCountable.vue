<template>
  <q-input :model-value="modelValue" type="textarea" @update:modelValue="$emit('update:modelValue', $event)"
    bottom-slots>
    <template v-slot:label>
      <slot />
    </template>
    <template v-slot:hint>
      <div :class="{ 'text-negative': current > maxLength }">{{ current }} / {{ maxLength }}</div>
    </template>
  </q-input>
</template>
<script>
import { defineComponent, computed } from 'vue';

export default defineComponent({
  name: 'InputCountable',
  props: {
    modelValue: {
      type: String,
      default: '',
    },
    maxLength: {
      type: Number,
      required: true,
    },
  },
  setup(props) {
    const current = computed(() => (props.modelValue ? [...props.modelValue].length : 0));
    return {
      current,
    };
  },
});
</script>
