<template>
  <b-form-group label="投稿一覧">
    <slot />
    <b-table hover :items="items" :fields="fields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" stacked="sm"
      class="clickable" @row-clicked="handleCheck">
      <template #head(select)>
        <b-form-checkbox :checked="all_selected" @change="handleToggle" />
      </template>
      <template #cell(select)="data">
        <b-form-checkbox :checked="checked(data.item.id)" @change="handleCheck(data.item)" />
      </template>
    </b-table>
    <div v-show="items.length === 0">
      投稿がありません
    </div>
  </b-form-group>
</template>
<script>
export default {
  props: ['articles', 'value'],
  data() {
    return {
      sortBy: 'modified_at',
      sortDesc: true,
      fields: []
    };
  },
  computed: {
    items() {
      return this.articles.map((a) =>
        Object.assign({}, a, {
          published_at: a.published_at.toFormat('yyyy/LL/dd HH:mm'),
          modified_at: a.modified_at.toFormat('yyyy/LL/dd HH:mm'),
          _rowVariant: this.rowValiant(a)
        })
      );
    },
    all_selected() {
      return this.value.length >= this.articles.length;
    }
  },
  created() {
    this.fields = [
      {
        key: 'select',
        label: '',
        sortable: false
      },
      {
        key: 'id',
        label: 'ID',
        sortable: true
      },
      {
        key: 'title',
        label: 'タイトル',
        sortable: true
      },
      {
        key: 'published_at',
        label: '作成日時',
        sortable: true
      },
      {
        key: 'modified_at',
        label: '更新日時',
        sortable: true
      }
    ];
  },
  methods: {
    rowValiant(article) {
      return this.checked(article.id) ? 'success' : '';
    },
    checked(id) {
      return this.value.includes(id);
    },
    handleCheck(item) {
      const index = this.value.indexOf(item.id);
      if (index === -1) {
        return this.value.push(item.id);
      }
      return this.value.splice(index, 1);
    },
    handleToggle() {
      if (this.all_selected) {
        return this.$emit('input', []);
      }
      return this.$emit(
        'input',
        this.articles.map((a) => a.id)
      );
    }
  }
};
</script>
