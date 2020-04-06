<template>
  <b-table hover :items="items" :fields="fields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc">
    <template v-slot:cell(action)="data">
      <b-button-group>
        <a :href="data.item.url" target="_blank" class="btn btn-sm btn-outline-secondary">Show</a>
        <b-button size="sm" variant="outline-primary" @click="handleEdit(data.item.id)">Edit</b-button>
      </b-button-group>
    </template>
  </b-table>
</template>
<script>
export default {
  props: ["articles"],
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
        a._rowVariant = this.rowValiant(a);
        return a;
      });
    }
  },
  methods: {
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
