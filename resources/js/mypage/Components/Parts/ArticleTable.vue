<template>
  <b-form-group :label="$t('Articles')">
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
    <div>{{$t('No article exists.')}}</div>
  </b-form-group>
</template>
<script>
import { DateTime } from "luxon";
export default {
  props: ["articles", "user"],
  name: "article-table",
  data() {
    return {
      sortBy: "id",
      sortDesc: false,
      fields: []
    };
  },
  created() {
    this.fields = [
      {
        key: "status",
        label: this.$t("Status"),
        sortable: true
      },
      {
        key: "post_type",
        label: this.$t("Post Type"),
        sortable: true
      },
      {
        key: "title",
        label: this.$t("Title"),
        sortable: true
      },
      {
        key: "views",
        label: "PV",
        sortable: true
      },
      {
        key: "conversions",
        label: "CV",
        sortable: true
      },
      {
        key: "created_at",
        label: this.$t("Created at"),
        sortable: true
      },
      {
        key: "updated_at",
        label: this.$t("Updated at"),
        sortable: true
      },
      {
        key: "action",
        label: "",
        sortable: false
      }
    ];
  },
  computed: {
    items() {
      return this.articles.map(a =>
        Object.assign({}, a, {
          status: this.$t(`statuses.${a.status}`),
          post_type: this.$t(`post_types.${a.post_type}`),
          created_at: a.created_at.toLocaleString(DateTime.DATETIME_FULL),
          updated_at: a.updated_at.toLocaleString(DateTime.DATETIME_FULL),
          _rowVariant: this.rowValiant(a)
        })
      );
    },
    can_edit() {
      return this.user.verified;
    }
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
    }
  }
};
</script>
