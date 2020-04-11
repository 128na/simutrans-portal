<template>
  <div>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{$t('Name')}}
      </template>
      <b-form-input type="text" v-model="params.name" :state="state('name')" autocomplete="name" />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{$t('Email')}}
      </template>
      <b-form-input
        type="email"
        v-model="params.email"
        :state="state('email')"
        autocomplete="email"
      />
    </b-form-group>
    <b-form-group>
      <template slot="label">
        <badge-required />
        {{$t('Password')}}
      </template>
      <form-password
        v-model="params.password"
        :state="state('password')"
        autocomplete="new-password"
      />
    </b-form-group>
  </div>
</template>
<script>
import { validatable } from "../../mixins";
export default {
  props: ["params"],
  mixins: [validatable]
};
</script>
