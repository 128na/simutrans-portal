<template>
  <div class="q-gutter-sm">
    <q-badge color="accent" v-for="tag in modelValue" :key="tag.id" class="no-wrap">
      <span class="q-pr-xs">
        {{ tag.name }}
      </span>
      <q-icon class="cursor-pointer" size="1rem" color="white" name="cancel" @click="handleClick(tag)" />
    </q-badge>
  </div>
  <q-dialog v-model="dialog" class="row">
    <custom-dialog class="col" style="max-width: 640px;">
      <template v-slot:title>タグの追加</template>
      <template v-slot:body>
        <q-item>
          <q-input label="検索" v-model="word" clearable @update:model-value="handleSearch" class="w-100" />
        </q-item>
        <q-card-actions v-show="word && !justMatch">
          <q-btn flat color="primary" @click="handleCreate(tag)">
            「{{ word }}」を新規作成
          </q-btn>
        </q-card-actions>
        <template v-for="tag in tags" :key="tag.id">
          <q-item clickable @click="handleClick(tag)">

            <q-item-section side v-show="exists(tag.id)">
              <q-icon name="check_circle" color="positive" />
            </q-item-section>
            <q-item-section>
              <q-item-label>{{ tag.name }}</q-item-label>
              <q-item-label caption>{{ tag.description || '説明がありません' }}</q-item-label>
            </q-item-section>
          </q-item>
        </template>
      </template>
    </custom-dialog>
  </q-dialog>
  <q-btn flat color="secondary" label="タグを追加" @click="dialog = true" />
</template>
<script>

import CustomDialog from 'src/components/Common/CustomDialog.vue';
import { useMypageApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';
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
    const dialog = ref(false);
    const word = ref('');
    const tags = ref([]);
    const { fetchTags, storeTag } = useMypageApi();
    let timer = null;
    const handler = useApiHandler();
    const handleSearch = () => {
      if (timer) {
        clearTimeout(timer);
      }
      timer = setTimeout(async () => {
        try {
          await handler.handle({
            doRequest: () => fetchTags(word.value || ''),
            done: (res) => { tags.value = res.data.data; },
          });
        } catch {
          // do nothing
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
      try {
        await handler.handleWithLoading({
          doRequest: () => storeTag(word.value),
          done: (res) => handleClick(res.data.data),
        });
      } catch {
        // do nothing
      }
    };
    return {
      tags,
      word,
      justMatch,
      exists,
      handleSearch,
      handleClick,
      handleCreate,
      dialog,
    };
  },
  components: { CustomDialog },
});

</script>
<style scoped>
.no-wrap {
  word-break: keep-all;
}
</style>
