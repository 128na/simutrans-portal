<template>
<div>
    <b-form-textarea
      :value="value"
      :rows="rows"
      :state="!isLimit && state"
      @input="$emit('input', $event)"
    />
    <small :class="{'text-danger': isLimit}">
        {{ currentLength }}
      <template v-if="maxLength">
         / {{ maxLength }}
      </template>
      文字
    </small>
    </div>
</template>
<script>
export default {
  props: {
    value: {
      type: String,
      default: ''
    },
    maxLength: {
      type: [Number, null],
      default: null
    },
    state: {
      type: [Boolean, null],
      default: null
    },
    rows: {
      type: Number,
      default: 8
    }
  },
  computed: {
    currentLength() {
      return this.value ? this.value.length : 0;
    },
    isLimit() {
      if (this.maxLength) {
        return this.maxLength < this.currentLength;
      }

      return false;
    }
  }
};
</script>
