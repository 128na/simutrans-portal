<template>
  <form class="w-100" @submit.prevent="search">
    <q-input :borderless="borderless" v-model="word" label="検索" @focus="focus=true" @blur="focus=false">
      <template v-slot:append>
        <q-icon v-if="word" name="close" @click="clear" class="cursor-pointer" />
        <q-icon name="search" @click="search" class="cursor-pointer" />
      </template>
    </q-input>
  </form>
</template>
<script>
import { defineComponent, ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';

export default defineComponent({
  name: 'SearchForm',
  setup() {
    const router = useRouter();
    const route = useRoute();

    const word = ref(route.query.word || '');
    const clear = () => {
      word.value = '';
    };
    const search = () => {
      router.push({ name: 'search', query: { word: word.value } });
    };

    const focus = ref(false);
    const borderless = computed(() => !focus.value && !word.value);

    return {
      word,
      clear,
      search,
      focus,
      borderless,
    };
  },
});
</script>
