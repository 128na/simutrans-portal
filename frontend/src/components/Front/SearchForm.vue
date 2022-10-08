<template>
  <form class="w-100" @submit.prevent="search">
    <q-input v-model="word" label="検索">
      <template v-slot:append>
        <q-icon v-if="word" name="close" @click="clear" class="cursor-pointer" />
        <q-icon name="search" @click="search" class="cursor-pointer" />
      </template>
    </q-input>
  </form>
</template>
<script>
import { defineComponent, ref } from 'vue';
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

    return {
      word,
      clear,
      search,
    };
  },
});
</script>
