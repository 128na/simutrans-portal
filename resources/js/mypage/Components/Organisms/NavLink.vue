<template>
  <router-link
    :to="to"
    v-slot="{ href, route, navigate, isActive, isExactActive }"
  >
    <b-nav-item
      :active="isExactActive"
      :href="href"
      @click="navigate"
      class="navnav"
    >
      <slot />
    </b-nav-item>
  </router-link>
</template>
<script>
export default {
  props: ["to"],
};
</script>
