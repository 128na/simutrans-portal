<template>
  <router-link
    :to="to"
    v-slot="{ href, route, navigate, isActive, isExactActive }"
    custom
  >
    <b-nav-item :active="isExactActive" :href="href" @click="navigate">
      <slot />
    </b-nav-item>
  </router-link>
</template>
<script>
export default {
  props: ["to"],
};
</script>
