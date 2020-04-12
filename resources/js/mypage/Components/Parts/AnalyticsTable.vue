<template>
  <b-form-group :label="$t('Articles')">
    <b-table
      hover
      :items="items"
      :fields="fields"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      @row-clicked="handleCheck"
      stacked="sm"
    >
      <template v-slot:head(select)>
        <b-form-checkbox :checked="all_selected" @change="handleToggle" />
      </template>
      <template v-slot:cell(select)="data">
        <b-form-checkbox :checked="checked(data.item.id)" @change="handleCheck(data.item)" />
      </template>
    </b-table>
    <div v-show="items.length === 0">{{$t('No article exists.')}}</div>
  </b-form-group>
</template>
<script>
import { DateTime } from "luxon";
export default {
  props: ["articles", "value"],
  name: "article-table",
  data() {
    return {
      sortBy: "updated_at",
      sortDesc: true,
      fields: []
    };
  },
  created() {
    this.fields = [
      {
        key: "select",
        label: "",
        sortable: false
      },
      {
        key: "id",
        label: "ID",
        sortable: true
      },
      {
        key: "title",
        label: this.$t("Title"),
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
      }
    ];
  },
  computed: {
    items() {
      return this.articles.map(a =>
        Object.assign({}, a, {
          created_at: a.created_at.toLocaleString(DateTime.DATETIME_FULL),
          updated_at: a.updated_at.toLocaleString(DateTime.DATETIME_FULL),
          _rowVariant: this.rowValiant(a)
        })
      );
    },
    all_selected() {
      return this.value.length >= this.articles.length;
    },
    toggle_button_text() {
      return this.all_selected ? "Unselect All" : "Select All";
    }
  },
  methods: {
    rowValiant(article) {
      return this.checked(article.id) ? "success" : "";
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
        return this.$emit("input", []);
      }
      return this.$emit(
        "input",
        this.articles.map(a => a.id)
      );
    }
  }
};
</script>
