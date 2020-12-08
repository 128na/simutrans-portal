<template>
  <b-form>
    <b-form-group>
      <template slot="label">
        <badge-required />
        名前
      </template>
      <b-form-input type="text" v-model="params.name" autocomplete="name" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        メールアドレス
      </template>
      <b-form-input type="email" v-model="params.email" autocomplete="email" />
    </b-form-group>
    <slot />
  </b-form>
</template>
<script>
export default {
  props: ["params"],
};
</script>
