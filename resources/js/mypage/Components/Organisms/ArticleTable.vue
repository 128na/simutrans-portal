<template>
  <div>
    <b-form-group>
      <b-button v-b-toggle.fieldSelect variant="secondary">表示項目</b-button>
      <b-collapse id="fieldSelect" class="mt-2">
        <b-card>
          <b-form-checkbox-group v-model="activeFields" :options="checkboxFields" stacked />
        </b-card>
      </b-collapse>
    </b-form-group>
    <b-form-group>
      <b-table hover :items="items" :fields="fields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" stacked="sm"
        class="clickable" @row-clicked="handleRowClicked" />
      <div v-show="items.length === 0">
        投稿がありません
      </div>
      <article-tooltip-menu v-if="selected_item" :item="selected_item" :style="tooltip_style"
        @close="handleTooltipClose" />
    </b-form-group>
  </div>
</template>
<script>
import { ARTICLE_FIELDS } from '../../../const';
export default {
  props: {
    articles: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      sortBy: 'modified_at',
      sortDesc: true,
      x: 0,
      y: 0,
      selected_item: null,
      activeFields: [
        'status',
        'post_type',
        'title',
        'totalViewCount',
        'totalConversionCount',
        'published_at',
        'modified_at'
      ]
    };
  },
  computed: {
    items() {
      return this.articles.map((a) =>
        Object.assign({}, a, {
          status: this.status(a.status),
          post_type: this.post_type(a.post_type),
          published_at: a.published_at ? a.published_at.toFormat('yyyy/LL/dd HH:mm') : '-',
          modified_at: a.modified_at.toFormat('yyyy/LL/dd HH:mm'),
          totalViewCount: a.metrics.totalViewCount,
          totalConversionCount: a.metrics.totalConversionCount,
          totalRetweetCount: a.metrics.totalRetweetCount,
          totalReplyCount: a.metrics.totalReplyCount,
          totalLikeCount: a.metrics.totalLikeCount,
          totalQuoteCount: a.metrics.totalQuoteCount,
          totalImpressionCount: a.metrics.totalImpressionCount,
          totalUrlLinkClicks: a.metrics.totalUrlLinkClicks,
          totalUserProfileClicks: a.metrics.totalUserProfileClicks,
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
    },
    checkboxFields() {
      return ARTICLE_FIELDS.map(f => {
        return { text: `${f.label}：${f.desc}`, value: f.key };
      });
    },
    fields() {
      return ARTICLE_FIELDS.filter(f => this.activeFields.includes(f.key));
    }
  },
  created() {
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
        case 'reservation':
          return '予約投稿';
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
