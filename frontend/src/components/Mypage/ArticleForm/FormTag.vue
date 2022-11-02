<template>
  <div class="q-gutter-sm">
    <q-badge color="accent" v-for="tag in modelValue" :key="tag.id" class="no-wrap">
      <span class="q-pr-xs">
        {{tag.name}}
      </span>
      <q-icon class="cursor-pointer" size="1rem" color="white" name="cancel" @click="handleClick(tag)" />
    </q-badge>
  </div>
  <q-btn flat color="secondary" label="タグを追加">
    <q-menu anchor="bottom left" self="top left">
      <q-item>
        <q-input label="検索" v-model="word" :loading="loading" clearable @update:model-value="handleSearch" />
      </q-item>
      <q-item v-show="word && !justMatch" clickable @click="handleCreate(tag)">
        <q-item-section>
          「{{word}}」を新規作成
        </q-item-section>
      </q-item>
      <template v-for="tag in tags" :key="tag.id">
        <q-item clickable @click="handleClick(tag)">

          <q-item-section side v-show="exists(tag.id)">
            <q-icon name="check_circle" color="positive" />
          </q-item-section>
          <q-item-section>
            {{tag.name}}
          </q-item-section>
        </q-item>
      </template>
    </q-menu>
  </q-btn>
</template>
<script>

import { useMypageApi } from 'src/composables/api';
import { defineComponent, ref, computed } from 'vue';

export default defineComponent({
  name: 'FormTag',
  props: {
    modelValue: {
      type: Array,
      default: () => [],
    },
  },

  setup(props, { emit }) {
    const loading = ref(false);
    const word = ref('');
    const tags = ref([]);

    const { fetchTags, storeTag } = useMypageApi();
    let timer = null;
    const handleSearch = () => {
      if (timer) {
        clearTimeout(timer);
      }
      timer = setTimeout(async () => {
        loading.value = true;
        try {
          const res = await fetchTags(word.value || '');
          tags.value = res.data.data;
        } catch (error) {
          // atode
        } finally {
          loading.value = false;
        }
      }, 500);
    };
    handleSearch();

    const exists = (id) => props.modelValue.some((t) => t.id === id);

    const handleClick = (tag) => {
      if (exists(tag.id)) {
        emit('update:model-value', props.modelValue.filter((t) => t.id !== tag.id));
      } else {
        emit('update:model-value', [...props.modelValue, tag]);
      }
    };

    const justMatch = computed(() => tags.value.some((t) => t.name === word.value));

    const handleCreate = async () => {
      loading.value = true;
      try {
        const res = await storeTag(word.value);
        handleClick(res.data.data);
      } catch (error) {
        // atode
      } finally {
        loading.value = false;
      }
    };
    return {
      loading,
      tags,
      word,
      justMatch,
      exists,
      handleSearch,
      handleClick,
      handleCreate,
    };
  },
});

</script>
<style scoped>
.no-wrap {
  word-break: keep-all;
}
</style>
