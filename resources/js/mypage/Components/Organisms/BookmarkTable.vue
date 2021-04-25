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
    <div v-show="items.length === 0">投稿ブックマークがありません</div>
    <bookmark-tooltip-menu
      v-if="selected_item"
      :item="selected_item"
      :style="tooltip_style"
      @close="handleTooltipClose"
    />
  </b-form-group>
</template>
<script>
export default {
  props: ["bookmarks"],
  data() {
    return {
      sortBy: "created_at",
      sortDesc: true,
      fields: [],
      x: 0,
      y: 0,
      selected_item: null,
    };
  },
  created() {
    this.fields = [
      {
        key: "title",
        label: "ブックマーク名",
        sortable: true,
      },
      {
        key: "public",
        label: "公開ステータス",
        sortable: true,
      },
      {
        key: "created_at",
        label: "作成日時",
        sortable: true,
      },
      {
        key: "bookmarkItemCount",
        label: "アイテム数",
        sortable: true,
      },
    ];
  },
  computed: {
    items() {
      return this.bookmarks.map((b) =>
        Object.assign({}, b, {
          bookmarkItemCount: b.bookmarkItems.length,
          public: b.is_public ? "公開" : "非公開",
          _rowVariant: b.is_public ? "" : "secondary",
        })
      );
    },
    tooltip_style() {
      return {
        position: "absolute",
        top: `${this.y}px`,
        left: `${this.x}px`,
      };
    },
  },
  methods: {
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
    },
  },
};
</script>
