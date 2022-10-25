<template>
  <q-page>
    PageAnyltics
    <apexchart width="500" type="bar" :options="options" :series="series" />
  </q-page>
</template>
<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'PageAnyltics',
  components: {},
  setup() {
    const options = {
      chart: {
        id: 'vuechart-example',
      },
      xaxis: {
        categories: [1991, 1992, 1993, 1994, 1995, 1996, 1997, 1998],
      },
    };
    const series = [{
      name: 'series-1',
      data: [30, 40, 45, 50, 49, 60, 70, 91],
    }];
    return {
      options,
      series,
    };
  },
});
</script>
