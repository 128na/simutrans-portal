<template>
  <b-form-group>
    <b-table
      hover
      :items="items"
      :fields="fields"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      stacked="sm"
      class="clickable"
      @row-clicked="handleRowClicked"
    />
    <div v-show="items.length === 0">
      投稿がありません
    </div>
    <article-tooltip-menu
      v-if="selected_item"
      :item="selected_item"
      :style="tooltip_style"
      @close="handleTooltipClose"
    />
  </b-form-group>
</template>
<script>
export default {
  props: ['articles'],
  data() {
    return {
      sortBy: 'updated_at',
      sortDesc: true,
      fields: [],
      x: 0,
      y: 0,
      selected_item: null
    };
  },
  computed: {
    items() {
      return this.articles.map((a) =>
        Object.assign({}, a, {
          status: this.status(a.status),
          post_type: this.post_type(a.post_type),
          created_at: a.created_at.toFormat('yyyy/LL/dd HH:mm'),
          updated_at: a.updated_at.toFormat('yyyy/LL/dd HH:mm'),
          _rowVariant: this.rowValiant(a)
        })
      );
    },
    tooltip_style() {
      return {
        position: 'absolute',
        top: `${this.y}px`,
        left: `${this.x}px`
      };
    }
  },
  created() {
    this.fields = [
      {
        key: 'status',
        label: 'ステータス',
        sortable: true
      },
      {
        key: 'post_type',
        label: '形式',
        sortable: true
      },
      {
        key: 'title',
        label: 'タイトル',
        sortable: true
      },
      {
        key: 'views',
        label: 'PV',
        sortable: true
      },
      {
        key: 'conversions',
        label: 'CV',
        sortable: true
      },
      {
        key: 'created_at',
        label: '作成日時',
        sortable: true
      },
      {
        key: 'updated_at',
        label: '更新日時',
        sortable: true
      }
    ];
  },
  methods: {
    rowValiant(article) {
      switch (article.status) {
        case 'private':
        case 'trash':
        case 'draft':
          return 'secondary';
        case 'publish':
        default:
          return '';
      }
    },
    status(status) {
      switch (status) {
        case 'publish':
          return '公開';
        case 'draft':
          return '下書き';
        case 'private':
          return '非公開';
        case 'trash':
          return 'ゴミ箱';
      }
    },
    post_type(post_type) {
      switch (post_type) {
        case 'addon-post':
          return 'アドオン投稿';
        case 'addon-introduction':
          return 'アドオン紹介';
        case 'page':
          return '一般記事';
        case 'markdown':
          return '一般記事(markdown)';
      }
    },
    handleRowClicked(item, index, event) {
      // 選択中に再度同じアイテムを選択したら選択解除
      if (this.selected_item && this.selected_item.id === item.id) {
        this.selected_item = null;
        return;
      }
      this.selected_item = item;
      const offset = 10;

      // 現在位置（右は時に近い場合は固定値）
      const innerWidth = window.innerWidth;
      const clientX = event.clientX;
      this.x = Math.min(innerWidth - 250, clientX + offset);

      // 現在位置+スクロール距離
      const scrollY = window.scrollY;
      const clientY = event.clientY;
      this.y = scrollY + clientY + offset;
    },
    handleTooltipClose() {
      this.selected_item = null;
    }
  }
};
</script>
