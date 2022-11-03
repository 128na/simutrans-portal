<template>
  <q-table :rows="rows" :columns="columns" row-key="id" :filter="filter" :rows-per-page-options="[20, 0]">
    <template v-slot:top-right>
      <q-input borderless dense v-model="filter" placeholder="Search">
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
    </template>
    <template v-slot:header="props">
      <q-tr :props="props">
        <q-th auto-width />
        <q-th v-for="col in props.cols" :key="col.name" :props="props">
          {{ col.label }}
        </q-th>
      </q-tr>
    </template>
    <template v-slot:body="props">
      <q-tr :props="props">
        <q-td auto-width>
          <q-btn size="sm" color="secondary" round dense @click="props.expand = !props.expand"
            :icon="props.expand ? 'remove' : 'add'" />
        </q-td>
        <q-td v-for="col in props.cols" :key="col.name" :props="props">
          {{ col.value }}
        </q-td>
      </q-tr>
      <q-tr v-if="props.expand" :props="props">
        <q-td colspan="100%">
          <slot :props="props" />
        </q-td>
      </q-tr>
    </template>
  </q-table>
</template>
<script>
import { defineComponent, ref } from 'vue';

export default defineComponent({
  name: 'PageAdminArticles',
  components: {},
  props: {
    columns: {
      type: Array,
      required: true,
    },
    rows: {
      type: Array,
      required: true,
    },
  },
  setup() {
    return {
      filter: ref(''),

    };
  },
});
</script>
