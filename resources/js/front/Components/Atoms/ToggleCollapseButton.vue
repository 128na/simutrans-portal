<template>
  <b-nav-text class="togglable clickable" link-classes="d-inline-block" :class="{'togglable-open': open}"
    @click="$emit('click')">
    <slot />
  </b-nav-text>
</template>
<script>

export default {
  props: {
    open: {
      type: Boolean,
      default: false
    }
  }
};
</script>

<style lang="scss" scoped>
.togglable {
  &::before {
    content: "";
    display: inline-block;
    vertical-align: baseline;
    margin-bottom: 0.2rem;
    border-right: 4px solid transparent;
    border-left: 4px solid transparent;
    border-bottom: 4px solid var(--white);
    border-top: none;
    transition: transform .3s ease;
  }

  &.togglable-open {
    &::before {
      transform: rotate(180deg);
    }
  }
}
</style>
