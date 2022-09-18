<template>
  <b-form-group>
    <b-form-radio-group :checked="value" :options="options" buttons button-variant="outline-secondary"
      @input="handleInput" />
  </b-form-group>
</template>
<script>
export default {
  props: {
    value: {
      type: String,
      required: false
    }
  },
  computed: {
    options() {
      return [
        { text: '簡易', value: 'list' },
        { text: '詳細', value: 'show' }
      ];
    }
  },
  methods: {
    handleInput(value) {
      this.$emit('input', value);
    }
  }
};
</script>
