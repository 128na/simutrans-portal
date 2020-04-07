<template>
  <b-input-group>
    <b-form-input
      :type="type"
      :value="value"
      :autocomplete="autocomplete"
      @input="$emit('input', $event)"
    />
    <template v-slot:append>
      <b-input-group-text @mousedown="showPassword" @mouseup="hidePassword">
        <b-icon :icon="icon" />
      </b-input-group-text>
    </template>
  </b-input-group>
</template>
<script>
export default {
  props: ["value", "autocomplete"],
  data() {
    return {
      show: false
    };
  },
  computed: {
    type() {
      return this.show ? "text" : "password";
    },
    icon() {
      return this.show ? "eye-fill" : "eye-slash-fill";
    }
  },
  methods: {
    showPassword() {
      this.show = true;
    },
    hidePassword() {
      this.show = false;
    }
  }
};
</script>
