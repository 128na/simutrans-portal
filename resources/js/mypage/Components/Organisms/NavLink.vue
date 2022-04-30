<template>
  <router-link
    v-slot="{ href, navigate, isExactActive }"
    :to="to"
    custom
  >
    <b-nav-item
      :active="isExactActive"
      :href="href"
      @click="navigate"
    >
      <slot />
    </b-nav-item>
  </router-link>
</template>
<script>
export default {
  props: ['to']
};
</script>
