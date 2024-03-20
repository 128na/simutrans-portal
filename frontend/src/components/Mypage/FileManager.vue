<template>
  <q-dialog v-model="show" maximized>
    <q-layout view="hHh lpR fFf">
      <q-header elevated class="bg-dark text-white">
        <q-toolbar>
          <q-toolbar-title>ファイル管理</q-toolbar-title>
          <q-space />
          表示
          <q-btn-toggle flat :options="displayCols" v-model="displayCol" />
          <q-btn dense flat icon="close" v-close-popup />
        </q-toolbar>
      </q-header>

      <q-page-container class="bg-white">
        <q-page>
          <div class="row">
            <div v-for="file in files" :key="file.id" :class="colClass">
              <q-img :src="file.thumbnail" ratio="1" fit="contain" class="bg-grey-1" @click="handleSelect(file.id)"
                v-close-popup="!isMultiSelect">
                <q-btn round icon="close" class="absolute all-pointer-events cursor-pointer"
                  style="top: 8px; right: 8px" color="negative" size="sm" @click.stop="handleDelete(file.id)" />
                <div class="text-h5 absolute-bottom text-center">
                  <q-btn v-show="isSelected(file.id)" round icon="check" size="sm" color="positive" />
                  {{ file.original_name }}
                </div>
              </q-img>
            </div>
          </div>
        </q-page>
      </q-page-container>

      <q-footer elevated class="bg-white">
        <q-toolbar>

          <q-checkbox v-model="autoCrop" class="text-dark q-mr-sm" label="自動トリミング">
            <q-tooltip>
              画像アップロードの場合、自動でトリミングします。<br />スクリーンショットのメニューバーをトリミングする時に便利です。
            </q-tooltip>
          </q-checkbox>
          <template v-if="autoCrop">
            <q-input type="number" min="0" max="128" v-model="crop.top" label="上" />
            <q-input type="number" min="0" max="128" v-model="crop.bottom" label="下" />
            <q-input type="number" min="0" max="128" v-model="crop.left" label="左" />
            <q-input type="number" min="0" max="128" v-model="crop.right" label="右" />
          </template>

          <q-space />
          <q-file borderless label-color="primary" type="file" label="新規アップロード" :max-file-size="20_000_000"
            :multiple="isMultiSelect" :max-files="10" :accept="accept" @update:model-value="handleUpload" />
          <q-btn v-show="isMultiSelect" color="secondary" v-close-popup>閉じる</q-btn>
        </q-toolbar>
      </q-footer>

    </q-layout>
  </q-dialog>
  <q-btn outline label="選択" color="secondary" @click="show = true" />
</template>
<script>
import { useQuasar } from 'quasar';
import { useMypageApi } from 'src/composables/api';
import { useNotify } from 'src/composables/notify';
import { useMypageStore } from 'src/store/mypage';
import { defineComponent, ref, computed } from 'vue';

const displayCols = [
  { value: 1, label: '1' },
  { value: 3, label: '3' },
  { value: 6, label: '6' },
  { value: 12, label: '12' },
];

export default defineComponent({
  name: 'FileManager',
  props: {
    modelValue: {
      type: [Number, Array],
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
    const isMultiSelect = computed(() => Array.isArray(props.modelValue));
    const accept = computed(() => (props.onlyImage ? 'image/*' : ''));
    const autoCrop = ref(false);
    const crop = ref({
      top: 32,
      bottom: 17,
      left: 0,
      right: 0,
    });

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

    const handleSelect = (id) => {
      if (isMultiSelect.value) {
        if (props.modelValue.includes(id)) {
          emit('update:model-value', props.modelValue.filter((v) => v !== id));
        } else {
          emit('update:model-value', [...props.modelValue, id]);
        }
      } else {
        if (props.modelValue === id) {
          emit('update:model-value', null);
        } else {
          emit('update:model-value', id);
        }
      }
    };

    const { storeAttachment, deleteAttachment } = useMypageApi();
    const $q = useQuasar();
    const notify = useNotify();
    const handleUpload = async (uploadFiles) => {
      $q.loading.show();
      if (!Array.isArray(uploadFiles)) {
        uploadFiles = [uploadFiles];
      }
      try {
        const formData = new FormData();
        for (const f of uploadFiles) {
          formData.append('files[]', f);
        }
        formData.append('only_image', props.onlyImage ? 1 : 0);
        if (autoCrop.value) {
          formData.append('crop[top]', crop.value.top);
          formData.append('crop[bottom]', crop.value.bottom);
          formData.append('crop[left]', crop.value.left);
          formData.append('crop[right]', crop.value.right);
        }

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

    const isSelected = (id) => {
      if (isMultiSelect.value) {
        return props.modelValue.includes(id);
      }
      return props.modelValue === id;
    };

    const displayCol = ref($q.platform.is.mobile ? 1 : 6);
    const colClass = computed(() => `col-${12 / displayCol.value}`);

    return {
      show,
      files,
      accept,
      handleSelect,
      handleUpload,
      handleDelete,
      isMultiSelect,
      isSelected,
      displayCols,
      displayCol,
      colClass,
      autoCrop,
      crop,
    };
  },
});
</script>
<style scoped>
.min-50 {
  min-width: 50vw;
}
</style>
