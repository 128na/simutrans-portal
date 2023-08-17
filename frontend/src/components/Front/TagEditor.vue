<template>
  <template v-if="tag.editable">
    <q-btn flat color="primary" label="編集する" @click="dialog = true" />
    <q-btn flat color="negative" v-if="auth.isAdmin" label="編集禁止" @click="toggle" />
  </template>
  <template v-else>
    <q-btn flat color="primary" label="編集できません" disabled />
    <q-btn flat color="positive" v-if="auth.isAdmin" label="編集解放" @click="toggle" />
  </template>
  <q-dialog v-model="dialog" class="row">
    <custom-dialog class="col" style="max-width: 640px;">
      <template v-slot:title>
        {{ tag.name }} の説明を編集
      </template>
      <template v-slot:body>
        <q-card-section>
          <input-countable v-model="description" :maxLength="1024" label>
            説明
          </input-countable>
        </q-card-section>
        <q-card-actions>
          <q-btn color="primary" label="保存" @click="update" />
          <q-btn flat color="negative" label="閉じる" v-close-popup />
        </q-card-actions>
      </template>
    </custom-dialog>
  </q-dialog>
</template>
<script>
import { useAuthStore } from 'src/store/auth';
import {
  defineComponent, ref, watch,
} from 'vue';
import { useMypageApi, useAdminApi } from 'src/composables/api';
import { useApiHandler } from 'src/composables/apiHandler';
import InputCountable from '../Common/Input/InputCountable.vue';
import CustomDialog from '../Common/CustomDialog.vue';

export default defineComponent({
  name: 'TagEditor',
  props: {
    tag: {
      type: Object,
      required: true,
    },
  },
  components: {
    InputCountable,
    CustomDialog,
  },
  setup(props) {
    const description = ref('');
    watch(props, (p) => {
      description.value = p.tag.description;
    }, { immediate: true, deep: true });
    const dialog = ref(false);
    const auth = useAuthStore();

    const mypage = useMypageApi();
    const admin = useAdminApi();
    const handler = useApiHandler();

    const toggle = async () => {
      try {
        await handler.handleWithLoading({
          doRequest: () => admin.toggleEditableTag(props.tag.id),
          done: () => window.location.reload(),
        });
      } catch {
        // do nothing
      }
    };
    const update = async () => {
      try {
        await handler.handleWithLoading({
          doRequest: () => mypage.updateTag(props.tag.id, description.value),
          done: () => window.location.reload(),
        });
      } catch {
        // do nothing
      }
    };

    return {
      description,
      dialog,
      auth,
      toggle,
      update,
    };
  },
});
</script>
