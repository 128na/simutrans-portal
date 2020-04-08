<template>
  <b-table
    hover
    :items="items"
    :fields="fields"
    :sort-by.sync="sortBy"
    :sort-desc.sync="sortDesc"
    stacked="sm"
  >
    <template v-slot:cell(action)="data">
      <b-button-group>
        <a
          v-if="isPublish(data.item)"
          :href="data.item.url"
          target="_blank"
          class="btn btn-sm btn-outline-secondary"
        >Show</a>
        <b-button
          size="sm"
          variant="outline-primary"
          :disabled="!can_edit"
          @click="handleEdit(data.item.id)"
        >Edit</b-button>
      </b-button-group>
    </template>
  </b-table>
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
      fields: [
        {
          key: "id",
          label: "ID",
          sortable: true
        },
        {
          key: "status",
          label: "Status",
          sortable: true
        },
        {
          key: "post_type",
          label: "Post Type",
          sortable: true
        },
        {
          key: "title",
          label: "Title",
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
          label: "Created At",
          sortable: true
        },
        {
          key: "updated_at",
          label: "Updated At",
          sortable: true
        },
        {
          key: "action",
          label: "Action",
          sortable: false
        }
      ]
    };
  },
  computed: {
    items() {
      return this.articles.map(a => {
        a.created_at = a.created_at.toLocaleString(DateTime.DATETIME_FULL);
        a.updated_at = a.updated_at.toLocaleString(DateTime.DATETIME_FULL);
        a._rowVariant = this.rowValiant(a);
        return a;
      });
    },
    can_edit() {
      return this.user.verified;
    }
  },
  methods: {
    isPublish(article) {
      return article.status === "publish";
    },
    handleEdit(id) {
      this.$router.push({
        name: "editArticle",
        params: { id }
      });
    },
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
