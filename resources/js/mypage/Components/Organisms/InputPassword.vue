<template>
  <b-input-group>
    <b-form-input :type="type" :value="value" :autocomplete="autocomplete" :state="state"
      @input="$emit('input', $event)" />
    <template #append>
      <b-input-group-text @click="toggle">
        <b-icon :icon="icon" />
      </b-input-group-text>
    </template>
  </b-input-group>
</template>
<script>
export default {
  props: ['value', 'autocomplete', 'state'],
  data() {
    return {
      show: false
    };
  },
  computed: {
    type() {
      return this.show ? 'text' : 'password';
    },
    icon() {
      return this.show ? 'eye-fill' : 'eye-slash-fill';
    }
  },
  methods: {
    toggle() {
      this.show = !this.show;
    }
  }
};
</script>
