<template>
  <div>
    <b-input-group>
      <slot :processing="processing" />
      <component :is="appendable">
        <b-button v-if="processing" variant="outline-primary" disabled>
          <b-spinner small class="mr-1" />処理中...
        </b-button>
        <b-button v-else variant="outline-primary" :disabled="!canDownload" @click="handleDownload">
          {{ buttonName }}
        </b-button>
      </component>
    </b-input-group>
    <div v-show="errorMessage" class="text-danger">
      {{ errorMessage }}
    </div>
  </div>
</template>
<script>
import api from '../../api';
import {
  TARGET_TYPE_USER,
  RETRY_LIMIT,
  RETRY_INTERVAL
} from '../../../const';
export default {
  props: ['target_type', 'target_id'],
  data() {
    return {
      processing: false,
      errorMessage: null,
      retry: 0
    };
  },
  computed: {
    appendable() {
      return this.$slots.default ? 'b-input-group-append' : 'div';
    },
    buttonName() {
      switch (this.target_type) {
        case TARGET_TYPE_USER:
          return 'エクスポート';
      }
      return '';
    },
    canDownload() {
      switch (this.target_type) {
        case TARGET_TYPE_USER:
          return true;
      }
      return false;
    }
  },
  methods: {
    async handleDownload() {
      this.beforeRequest();
      const res = await this.handleApi().catch((e) => e.response);

      if (!res || res.status !== 200 || this.retry > RETRY_LIMIT) {
        return this.failed();
      }

      if (res.data.generated) {
        return this.successed(res.data);
      } else {
        return this.retryRequest();
      }
    },
    async handleApi() {
      switch (this.target_type) {
        case TARGET_TYPE_USER:
          return api.fetchUserBulkZip();
      }
    },
    beforeRequest() {
      this.errorMessage = null;
      this.processing = true;
    },
    retryRequest() {
      this.retry++;
      setTimeout(this.handleDownload, RETRY_INTERVAL);
    },
    successed({ url }) {
      this.errorMessage = null;
      this.processing = false;
      this.retry = 0;
      window.open(url);
    },
    failed() {
      this.errorMessage = '生成に失敗しました';
      this.processing = false;
      this.retry = 0;
    }
  }
};
</script>
