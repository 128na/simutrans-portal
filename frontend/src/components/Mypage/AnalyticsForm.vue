<template>
  <div class="q-pa-md">
    <div class="q-gutter-md">
      <div>
        <api-error-message :message="analytics.errorMessage" />
      </div>
      <div>
        <q-input v-model="analytics.startDate" label="開始日" />
        <q-input v-model="analytics.endDate" label="終了日">
          <template v-slot:append>
            <q-icon name="event" class="cursor-pointer">
              <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                <q-date v-model="analytics.dateRange" range mask="YYYY/MM/DD">
                  <div class="row items-center justify-end">
                    <q-btn v-close-popup label="閉じる" color="primary" flat />
                  </div>
                </q-date>
              </q-popup-proxy>
            </q-icon>
          </template>
        </q-input>
      </div>
      <!-- todo: グラフ方式の選択 -->
      <!-- todo: データ種類の選択 -->
      <div>
        <q-option-group v-model="analytics.type" :options="ANALYTICS_TYPES" color="primary" inline />
      </div>
      <div>
        <q-btn color="primary" label="取得" @click="analytics.fetch" />
      </div>
    </div>
  </div>
</template>
<script>
import { ANALYTICS_TYPES } from 'src/const';
import { useMypageStore } from 'src/store/mypage';
import { useAnalyticsStore } from 'src/store/analytics';
import { defineComponent } from 'vue';
import ApiErrorMessage from 'src/components/Common/ApiErrorMessage.vue';

export default defineComponent({
  name: 'AnalyticsForm',
  components: { ApiErrorMessage },
  setup() {
    const mypage = useMypageStore();
    const analytics = useAnalyticsStore();
    return {
      mypage,
      analytics,
      ANALYTICS_TYPES,
    };
  },
});
</script>
