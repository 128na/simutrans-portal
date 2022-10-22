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

      <q-page-container>
        <q-page>
          <div class="row">
            <div v-for="file in files" :key="file.id" class="col-xs-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
              <q-img :src="file.thumbnail" ratio="1" fit="contain" class="bg-grey-1">
                <div class="text-h5 absolute-bottom text-center">
                  {{ file.original_name }}
                </div>
              </q-img>
            </div>
          </div>
        </q-page>
      </q-page-container>

      <q-footer elevated class="bg-dark text-white">
        <q-toolbar>
          <q-btn flat>選択</q-btn>
          <q-space />
          <q-btn flat>アップロード</q-btn>
        </q-toolbar>
      </q-footer>

    </q-layout>
  </q-dialog>
  <q-btn :label="label" color="secondary" @click="show=true" />
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { useMypageStore } from 'src/store/mypage';
import { defineComponent, ref, computed } from 'vue';

export default defineComponent({
  name: 'FileManager',
  props: {
    modelValue: {
      type: Array,
      default: () => [],
    },
    label: {
      type: String,
      default: '選択',
    },
    imageOnly: {
      type: Boolean,
      default: false,
    },
  },
  setup(props) {
    const editor = useArticleEditStore();
    const mypage = useMypageStore();
    const show = ref(false);
    const files = computed(() => mypage.attachments.filter((a) => (props.imageOnly ? a.type === 'image' : true)));
    return {
      editor,
      show,
      files,
    };
  },
});
</script>
<style scoped>
.min-50 {
  min-width: 50vw;
}
</style>
