<template>
  <b-form-group label="投稿一覧">
    <b-table
      hover
      :items="items"
      :fields="fields"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      stacked="sm"
    >
      <template v-slot:cell(action)="data">
        <tooltip-menu :article="data.item" />
      </template>
    </b-table>
    <div v-show="items.length === 0">投稿がありません</div>
  </b-form-group>
</template>
<script>
import { DateTime } from "luxon";
export default {
  props: ["articles", "user"],
  data() {
    return {
      sortBy: "updated_at",
      sortDesc: true,
      fields: [],
    };
  },
  created() {
    this.fields = [
      {
        key: "status",
        label: "ステータス",
        sortable: true,
      },
      {
        key: "post_type",
        label: "形式",
        sortable: true,
      },
      {
        key: "title",
        label: "タイトル",
        sortable: true,
      },
      {
        key: "views",
        label: "PV",
        sortable: true,
      },
      {
        key: "conversions",
        label: "CV",
        sortable: true,
      },
      {
        key: "created_at",
        label: "作成日時",
        sortable: true,
      },
      {
        key: "updated_at",
        label: "更新日時",
        sortable: true,
      },
      {
        key: "action",
        label: "",
        sortable: false,
      },
    ];
  },
  computed: {
    items() {
      return this.articles.map((a) =>
        Object.assign({}, a, {
          status: this.status(a.status),
          post_type: this.post_type(a.post_type),
          created_at: a.created_at.toFormat("yyyy/LL/dd HH:mm"),
          updated_at: a.updated_at.toFormat("yyyy/LL/dd HH:mm"),
          _rowVariant: this.rowValiant(a),
        })
      );
    },
  },
  methods: {
    rowValiant(article) {
      switch (article.status) {
        case "private":
        case "trash":
        case "draft":
          return "secondary";
        case "publish":
        default:
          return "";
      }
    },
    status(status) {
      switch (status) {
        case "publish":
          return "公開";
        case "draft":
          return "下書き";
        case "private":
          return "非公開";
        case "trash":
          return "ゴミ箱";
      }
    },
    post_type(post_type) {
      switch (post_type) {
        case "addon-post":
          return "アドオン投稿";
        case "addon-introduction":
          return "アドオン紹介";
        case "page":
          return "一般記事";
        case "markdown":
          return "一般記事(markdown)";
      }
    },
  },
};
</script>
