<template>
  <q-list bordered separator class="pop-menu bg-default shadow-2" v-if="row">
    <q-item>
      <q-item-section>
        <q-item-label>
          {{row.id}}: {{row.title}}
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
    <q-item v-if="row.post_type === 'publish'">
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
import {
  defineComponent,
} from 'vue';

export default defineComponent({
  name: 'PopMenu',
  props: {
    row: {
      type: Object,
      required: true,
    },
  },
});
</script>

<style>
.pop-menu {
  position: fixed !important;
}
</style>
