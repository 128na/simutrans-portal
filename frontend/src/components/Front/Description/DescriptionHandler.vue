<template>
  <component :is="componentName" :description="description" />
</template>
<script>
import { defineComponent, computed } from 'vue';
import DescriptionProfile from 'src/components/Front/Description/DescriptionProfile.vue';
import DescriptionMessage from 'src/components/Front/Description/DescriptionMessage.vue';
import DescriptionTag from 'src/components/Front/Description/DescriptionTag.vue';

export default defineComponent({
  name: 'DescriptionHandler',
  props: {
    description: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const componentName = computed(() => {
      switch (props.description.type) {
        case 'profile':
          return DescriptionProfile;
        case 'message':
          return DescriptionMessage;
        case 'tag':
          return DescriptionTag;
        default:
          return null;
      }
    });

    return { componentName };
  },
});
</script>
