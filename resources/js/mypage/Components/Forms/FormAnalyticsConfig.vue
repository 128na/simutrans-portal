<template>
  <div>
    <b-form-group :label="$t('Types')">
      <b-form-radio-group
        v-model="value.type"
        :options="options.types"
        buttons
        button-variant="outline-primary"
      ></b-form-radio-group>
    </b-form-group>
    <b-form-group :label="$t('Aggregation method')">
      <b-form-radio-group
        v-model="value.mode"
        :options="options.modes"
        buttons
        button-variant="outline-primary"
      ></b-form-radio-group>
    </b-form-group>
    <b-form-group :label="$t('Target')">
      <b-form-checkbox-group v-model="value.axes" :options="options.axes"></b-form-checkbox-group>
    </b-form-group>
    <b-form-group :label="$t('Start Date')">
      <b-form-datepicker class="mr-2" v-model="computed_start_date"></b-form-datepicker>
    </b-form-group>
    <b-form-group :label="$t('End Date')">
      <b-form-datepicker v-model="computed_end_date"></b-form-datepicker>
    </b-form-group>
  </div>
</template>
<script>
import { DateTime } from "luxon";
import Interval from "luxon/src/interval.js";
import { analytics_constants } from "../../mixins";
export default {
  name: "form-analytics-config",
  props: ["value"],
  data() {
    return {
      options: {}
    };
  },
  created() {
    this.options = this.OPTIONS;
  },
  computed: {
    computed_start_date: {
      get() {
        return this.value.start_date.toISODate();
      },
      set(val) {
        return (this.value.start_date = DateTime.fromISO(val));
      }
    },
    computed_end_date: {
      get() {
        return this.value.end_date.toISODate();
      },
      set(val) {
        return (this.value.end_date = DateTime.fromISO(val));
      }
    }
  }
};
</script>
