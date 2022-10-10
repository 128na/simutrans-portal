<template>
  <q-list bordered separator class="pop-menu bg-default shadow-2">
    <q-item>
      <q-item-section>
        <q-item-label>
          {{store.state.row.id}}: {{store.state.row.title}}
        </q-item-label>
      </q-item-section>
    </q-item>
    <q-item>
      <q-item-section>
        <q-item-label>
          URLをコピー
        </q-item-label>
      </q-item-section>
    </q-item>
    <q-item>
      <q-item-section>
        <q-item-label>
          記事を表示
        </q-item-label>
      </q-item-section>
    </q-item>
    <q-item>
      <q-item-section>
        <q-item-label>
          編集
        </q-item-label>
      </q-item-section>
    </q-item>
    <q-item v-if="store.state.row.post_type === 'publish'">
      <q-item-section>
        <q-item-label>
          記事を非公開にする
        </q-item-label>
      </q-item-section>
    </q-item>
    <q-item v-else>
      <q-item-section>
        <q-item-label>
          記事を公開にする（自動ツイート無し）
        </q-item-label>
      </q-item-section>
    </q-item>
  </q-list>
</template>
<script>
import { usePopMenuStore } from 'src/store/popMenu';
import {
  defineComponent,
} from 'vue';

export default defineComponent({
  name: 'PopMenu',
  setup() {
    const store = usePopMenuStore();

    return { store };
  },
});
</script>

<style>
.pop-menu {
  position: fixed !important;
}
</style>
