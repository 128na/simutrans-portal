<template>
  <b-form-checkbox
    :value="value"
    :button-variant="variant"
    button
    class="mr-2"
    @input="handleInput"
  >
    {{ text }}
  </b-form-checkbox>
</template>
<script>
export default {
  props: ['value'],
  computed: {
    variant() {
      return this.value ? 'outline-success' : 'outline-secondary';
    },
    text() {
      return this.value ? '省略表示にする' : '詳細表示にする';
    }
  },
  methods: {
    handleInput(e) {
      this.$emit('input', !e);
    }
  }
};
</script>
