<template>
  <h2>[{{ status }}]{{ message }}</h2>
</template>
<script>
import { titleResolver } from '../../mixins';
export default {
  mixins: [titleResolver],
  computed: {
    status() {
      return this.$route.params.status;
    },
    message() {
      switch (this.status) {
        case 404:
          return 'ページが見つかりませんでした';
        default:
          return 'エラーが発生しました';
      }
    }
  }
};
</script>
