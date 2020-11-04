<template>
  <div>
    <b-form-group label="間隔">
      <b-form-radio-group
        v-model="value.type"
        :options="options.types"
        buttons
        button-variant="outline-primary"
      />
    </b-form-group>
    <b-form-group label="集計方式">
      <b-form-radio-group
        v-model="value.mode"
        :options="options.modes"
        buttons
        button-variant="outline-primary"
      />
    </b-form-group>
    <b-form-group label="対象">
      <b-form-checkbox-group v-model="value.axes" :options="options.axes" />
    </b-form-group>
    <b-form-group label="開始日">
      <b-form-datepicker
        class="mr-2"
        v-model="computed_start_date"
        :state="validationState('start_date')"
      />
      <validation-message field="start_date" />
    </b-form-group>
    <b-form-group label="終了日">
      <b-form-datepicker
        v-model="computed_end_date"
        :state="validationState('end_date')"
      />
      <validation-message field="end_date" />
    </b-form-group>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
import { DateTime, Interval } from "luxon";
import { analytics_constants } from "../../../mixins/analytics";
export default {
  mixins: [analytics_constants],
  props: ["value"],
  data() {
    return {
      options: {},
    };
  },
  created() {
    this.options = this.OPTIONS;
  },
  computed: {
    ...mapGetters(["validationState"]),
    computed_start_date: {
      get() {
        return this.value.start_date.toISODate();
      },
      set(val) {
        return (this.value.start_date = DateTime.fromISO(val));
      },
    },
    computed_end_date: {
      get() {
        return this.value.end_date.toISODate();
      },
      set(val) {
        return (this.value.end_date = DateTime.fromISO(val));
      },
    },
  },
};
</script>
