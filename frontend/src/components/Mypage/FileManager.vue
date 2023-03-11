<template>
  <q-dialog v-model="show" maximized>
    <q-layout view="hHh lpR fFf">
      <q-header elevated class="bg-dark text-white">
        <q-toolbar>
          <q-toolbar-title>ファイル管理</q-toolbar-title>
          <q-space />
          <q-btn dense flat icon="close" v-close-popup />
        </q-toolbar>
      </q-header>

      <q-page-container class="bg-white">
        <q-page>
          <div class="row">
            <div v-for="file in files" :key="file.id" class="col-xs-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
              <q-img :src="file.thumbnail" ratio="1" fit="contain" class="bg-grey-1" @click="handleSelect(file.id)"
                v-close-popup>
                <q-icon class="absolute all-pointer-events cursor-pointer" size="32px" name="cancel" color="negative"
                  style="top: 8px; right: 8px" @click.stop="handleDelete(file.id)" />
                <div class="text-h5 absolute-bottom text-center">
                  <q-icon v-show="file.id === modelValue" name="check_circle" size="1.5rem" color="positive" />
                  {{ file.original_name }}
                </div>
              </q-img>
            </div>
          </div>
        </q-page>
      </q-page-container>

      <q-footer elevated class="bg-white">
        <q-toolbar>
          <div class="text-dark">
            チェックの入っているファイルを再選択すると選択が解除されます。<br>
            画像は自動でwebpに変換、1280x720より大きい画像はリサイズされます。
          </div>
          <q-space />
          <q-file borderless label-color="primary" type="file" label="新規アップロード" :accept="accept"
            @update:model-value="handleUpload" />
        </q-toolbar>
      </q-footer>

    </q-layout>
  </q-dialog>
  <q-btn flat label="選択" color="secondary" @click="show = true" />
</template>
<script>
import { useQuasar } from 'quasar';
import { useMypageApi } from 'src/composables/api';
import { useNotify } from 'src/composables/notify';
import { useMypageStore } from 'src/store/mypage';
import { defineComponent, ref, computed } from 'vue';

export default defineComponent({
  name: 'FileManager',
  props: {
    modelValue: {
      type: Number,
      default: null,
    },
    attachmentableType: {
      type: String,
      default: 'Article',
    },
    attachmentableId: {
      type: Number,
    },
    onlyImage: {
      type: Boolean,
      default: false,
    },
  },
  setup(props, { emit }) {
    const mypage = useMypageStore();
    const show = ref(false);
    const files = computed(() => mypage.attachments
      .filter((a) => (props.onlyImage ? a.type === 'image' : true))
      .filter((a) => {
        if (a.attachmentable_id === null) {
          return true;
        }
        if (props.attachmentableId) {
          return a.attachmentable_id === props.attachmentableId && a.attachmentable_type === props.attachmentableType;
        }
        return false;
      }));

    const accept = computed(() => (props.onlyImage ? 'image/*' : ''));

    const handleSelect = (id) => {
      if (props.modelValue === id) {
        emit('update:model-value', null);
      } else {
        emit('update:model-value', id);
      }
    };

    const { storeAttachment, deleteAttachment } = useMypageApi();
    const $q = useQuasar();
    const notify = useNotify();
    const handleUpload = async (file) => {
      $q.loading.show();
      try {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('only_image', props.onlyImage ? 1 : 0);

        const res = await storeAttachment(formData);
        mypage.attachments = res.data.data;
        notify.success('アップロードしました');
      } catch (error) {
        notify.failed('アップロードに失敗しました');
      } finally {
        $q.loading.hide();
      }
    };

    const handleDelete = async (id) => {
      // eslint-disable-next-line no-alert
      if (!window.confirm('削除しますか？')) {
        return;
      }
      $q.loading.show();
      try {
        const res = await deleteAttachment(id);
        mypage.attachments = res.data.data;
        notify.success('削除しました');
      } catch (error) {
        notify.failed('削除に失敗しました');
      } finally {
        $q.loading.hide();
      }
    };
    return {
      show,
      files,
      accept,
      handleSelect,
      handleUpload,
      handleDelete,
    };
  },
});
</script>
<style scoped>
.min-50 {
  min-width: 50vw;
}
</style>
