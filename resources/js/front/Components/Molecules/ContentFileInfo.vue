<template>
  <div>
    <b-button v-if="hasDat" v-b-toggle.collapse-dat variant="outline-secondary" size="sm">Datファイル一覧</b-button>
    <b-collapse id="collapse-dat" class="my-2">
      <b-card body-class="bg-light">
        <li v-for="(names, filename) in file_info.dats" :key="filename">
          <span>{{ filename }}</span>
          <ul>
            <li v-for="name in names" :key="name">
              {{ name }}
            </li>
          </ul>
        </li>
      </b-card>
    </b-collapse>
    <b-button v-if="hasTab" v-b-toggle.collapse-tab variant="outline-secondary" size="sm">Tabファイル一覧</b-button>
    <b-collapse id="collapse-tab" class="my-2">
      <b-card body-class="bg-light">
        <li v-for="(names, filename) in file_info.tabs" :key="filename">
          <span>{{ filename }}</span>
          <ul>
            <li v-for="(translated, original) in names" :key="original">
              {{ translated }} ({{ original }})
            </li>
          </ul>
        </li>
      </b-card>
    </b-collapse>
  </div>

</template>
<script>
export default {
  props: {
    file_info: {
      type: Object,
      default: () => Object.create({ dats: {}, tabs: {} }),
    }
  },
  computed: {
    hasDat() {
      return this.file_info.dats && Object.keys(this.file_info.dats).length > 0;
    },
    hasTab() {
      return this.file_info.tabs && Object.keys(this.file_info.tabs).length > 0;
    }
  }
};
</script>
