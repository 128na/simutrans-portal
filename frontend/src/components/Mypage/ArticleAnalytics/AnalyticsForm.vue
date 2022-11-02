<template>
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
    <div>
      <div>間隔</div>
      <q-option-group v-model="analytics.type" :options="ANALYTICS_TYPES" color="primary" inline
        @update:model-value="analytics.fetch" />
    </div>
    <div>
      <div>集計方式</div>
      <q-option-group v-model="analytics.mode" :options="ANALYTICS_MODES" color="primary" inline />
    </div>
    <div>
      <div>対象</div>
      <q-option-group v-model="analytics.axes" :options="ANALYTICS_AXES" color="primary" inline type="checkbox" />
    </div>
    <div>
      <q-btn color="primary" label="取得" @click="analytics.fetch" />
    </div>
  </div>
</template>
<script>
import { ANALYTICS_TYPES, ANALYTICS_MODES, ANALYTICS_AXES } from 'src/const';
import { useMypageStore } from 'src/store/mypage';
import { useAnalyticsStore } from 'src/store/analytics';
import { defineComponent } from 'vue';
import ApiErrorMessage from 'src/components/Common/Text/ApiErrorMessage.vue';

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
      ANALYTICS_MODES,
      ANALYTICS_AXES,
    };
  },
});
</script>
