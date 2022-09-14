<template>
  <h2>[{{ status }}]{{ message }}</h2>
</template>
<script>
export default {
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
