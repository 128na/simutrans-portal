<template>
  <div v-if="isReservation">
    <q-input v-model="displayDateTime" label bottom-slots :error-message="editor.vali('article.published_at')"
      :error="!!editor.vali('article.published_at')">
      <template v-slot:label>
        <label-required>予約投稿日時</label-required>
      </template>
      <template v-slot:append>
        <q-icon name="event" class="cursor-pointer">
          <q-popup-proxy cover transition-show="scale" transition-hide="scale">
            <q-date v-model="displayDateTime" :mask="mask" :options="dateLimit">
              <div class="row items-center justify-end">
                <q-btn v-close-popup label="Close" color="primary" flat />
              </div>
            </q-date>
          </q-popup-proxy>
        </q-icon>
        <q-icon name="access_time" class="cursor-pointer">
          <q-popup-proxy cover transition-show="scale" transition-hide="scale">
            <q-time v-model="displayDateTime" :mask="mask" format24h>
              <div class="row items-center justify-end">
                <q-btn v-close-popup label="Close" color="primary" flat />
              </div>
            </q-time>
          </q-popup-proxy>
        </q-icon>
      </template>
    </q-input>
    <small>
      保存時刻より1時間後を指定できます。公開時刻は1分単位で指定できますが、サーバーの都合で実際に公開されるのは5分刻みの時刻になります。<br>
      また、投稿時には自動通知はされません。
    </small>
  </div>
</template>
<script>
import { useArticleEditStore } from 'src/store/articleEdit';
import { DT_FORMAT, D_FORMAT, defaultDateTime } from 'src/const';
import { defineComponent, computed } from 'vue';
import { DateTime } from 'luxon';
import LabelRequired from 'src/components/Common/LabelRequired.vue';

export default defineComponent({
  name: 'FormReservation',
  components: { LabelRequired },

  setup() {
    const editor = useArticleEditStore();
    const isReservation = computed(() => (editor.article.status === 'reservation'));
    const displayDateTime = computed({
      get() {
        const dt = DateTime.fromISO(editor.article.published_at);
        return dt.isValid ? dt.toFormat(DT_FORMAT) : editor.article.published_at;
      },
      set(val) {
        const dt = DateTime.fromFormat(val, DT_FORMAT);
        editor.article.published_at = dt.isValid ? dt.toISO() : val;
      },
    });
    const mask = 'YYYY/MM/DD HH:mm';
    const dateLimit = (date) => date >= defaultDateTime().toFormat(D_FORMAT);

    return {
      editor,
      isReservation,
      displayDateTime,
      mask,
      dateLimit,
    };
  },
});
</script>
