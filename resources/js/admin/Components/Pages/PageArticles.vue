<template>
  <div>
    <page-title>記事一覧</page-title>
    <page-description>
      記事の論理削除状態、ステータスの公開・非公開を変更できる。
    </page-description>
    <b-form inline class="mb-2">
      <detail-button v-model="detail" />
      <b-form-input v-model="search" placeholder="絞り込み" />
    </b-form>
    <b-table
      hover
      :items="articles"
      :fields="computed_fields"
      :filter="search"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      stacked="sm"
    >
      <template v-slot:cell(summary)="row">
        <span v-if="row.item.deleted_at">削</span>
        <span v-else-if="row.item.status === 'private'">非</span>
        <span v-else-if="row.item.status === 'publish'">公</span>
        <span v-else-if="row.item.status === 'draft'">下</span>
        <span v-else-if="row.item.status === 'trash'">捨</span>
      </template>
      <template v-slot:cell(title)="row">
        <a :href="linkUrl(row.item.slug)" target="_blank">
          {{ row.item.title }}
        </a>
      </template>
      <template v-slot:cell(user_id)="row">
        {{ row.item.user.name }}
      </template>
      <template v-slot:cell(action)="row">
        <div class="action-box d-flex">
          <div v-if="row.item.deleted_at">
            <b-button
              size="sm"
              variant="outline-danger"
              @click="toRestore(row.item)"
            >
              復元
            </b-button>
          </div>
          <div v-else>
            <b-button
              v-if="row.item.status === 'publish'"
              size="sm"
              variant="outline-danger"
              @click="toPrivate(row.item)"
            >
              非公開
            </b-button>
            <b-button
              v-else
              size="sm"
              variant="outline-danger"
              @click="toPublish(row.item)"
            >
              公開
            </b-button>
            <b-button
              size="sm"
              variant="outline-danger"
              @click="toDestroy(row.item)"
            >
              削除
            </b-button>
          </div>
        </div>
      </template>
    </b-table>
  </div>
</template>
<script>
import { mapGetters, mapActions } from "vuex";
export default {
  data() {
    return {
      sortBy: "id",
      sortDesc: true,
      search: "",
      detail: false,
      fields: [
        {
          key: "id",
          label: "ID",
          sortable: true,
        },
        {
          key: "summary",
          label: "",
          sortable: false,
        },
        {
          key: "user_id",
          label: "投稿者",
          sortable: true,
        },
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
          key: "deleted_at",
          label: "削除日時",
          sortable: true,
        },
        {
          key: "action",
          label: "操作",
          sortable: false,
        },
      ],
    };
  },
  created() {
    if (!this.articles.length) {
      this.fetchArticles();
    }
  },
  computed: {
    ...mapGetters(["articles"]),
    computed_fields() {
      const filter_keys = ["id", "summary", "user_id", "title", "action"];
      return this.detail
        ? this.fields.filter((f) => f.key !== "summary")
        : this.fields.filter((f) => filter_keys.includes(f.key));
    },
    base_url() {
      return process.env.MIX_APP_URL;
    },
  },
  methods: {
    ...mapActions(["fetchArticles", "updateArticle", "deleteArticle"]),
    linkUrl(slug) {
      return `${this.base_url}/articles/${slug}`;
    },
    toRestore(article) {
      if (confirm("復元しますか？")) {
        this.deleteArticle(article.id);
      }
    },
    toDestroy(article) {
      if (confirm("論理削除しますか")) {
        this.deleteArticle(article.id);
      }
    },
    toPrivate(article) {
      if (confirm("ステータスを非公開にしますか")) {
        this.updateArticle({ id: article.id, article: { status: "private" } });
      }
    },
    toPublish(article) {
      if (confirm("ステータスを公開にしますか")) {
        this.updateArticle({ id: article.id, article: { status: "publish" } });
      }
    },
  },
};
</script>
<style lang="scss" scoped>
.action-box {
  width: 8rem;
}
</style>
