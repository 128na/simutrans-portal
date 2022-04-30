<template>
  <b-form>
    <b-form-group>
      <template slot="label">
        <badge-required />
        名前
      </template>
      <b-form-input
        v-model="params.name"
        type="text"
        autocomplete="name"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        メールアドレス
      </template>
      <b-form-input
        v-model="params.email"
        type="email"
        autocomplete="email"
      />
    </b-form-group>
    <slot />
  </b-form>
</template>
<script>
export default {
  props: ['params']
};
</script>
