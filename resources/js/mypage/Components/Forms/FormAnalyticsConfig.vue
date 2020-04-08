<template>
  <div>
    <b-form-group label="Type">
      <b-form-radio-group
        v-model="value.type"
        :options="options.types"
        buttons
        button-variant="outline-primary"
      ></b-form-radio-group>
    </b-form-group>
    <b-form-group label="Aggregation">
      <b-form-radio-group
        v-model="value.mode"
        :options="options.modes"
        buttons
        button-variant="outline-primary"
      ></b-form-radio-group>
    </b-form-group>
    <b-form-group label="Axis">
      <b-form-checkbox-group v-model="value.axis" :options="options.axes"></b-form-checkbox-group>
    </b-form-group>
    <b-form inline class="mb-1">
      <b-form-group label="Start Date">
        <b-form-datepicker class="mr-2" v-model="computed_start_date"></b-form-datepicker>
      </b-form-group>
      <b-form-group label="End Date">
        <b-form-datepicker v-model="computed_end_date"></b-form-datepicker>
      </b-form-group>
    </b-form>
  </div>
</template>
<script>
import { DateTime } from "luxon";
import Interval from "luxon/src/interval.js";
export default {
  name: "form-analytics-config",
  props: ["value"],
  data() {
    return {
      options: {
        types: [
          { value: "daily", text: "daily" },
          { value: "monthly", text: "Monthly" },
          { value: "yearly", text: "Yearly" }
        ],
        modes: [
          { text: "Transition", value: "line" },
          { text: "Total", value: "sum" }
        ],
        axes: [
          { text: "Page Views", value: "pv" },
          { text: "Conversions", value: "cv" }
        ]
      }
    };
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
