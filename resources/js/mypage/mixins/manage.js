import axios from "axios";
import { DateTime } from "luxon";
export default {
  props: {
    scopes: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  data() {
    return {
      items: [],
      item: {},
      fetching: false,
      modal: null,
      endpoint: null,
    };
  },
  created() {
    this.fetch();
  },
  computed: {
    optionScopes() {
      return this.scopes.map((s) => {
        return {
          text: s.description,
          value: s.id,
        };
      });
    },
  },
  methods: {
    toDateTime(str, format = "fromISO") {
      return DateTime[format](str).toLocaleString(DateTime.DATETIME_FULL);
    },
    setItem(item = {}) {
      this.item = item;
    },
    scopeName(scope) {
      const s = this.scopes.find((s) => s.id === scope);
      if (s) {
        return s.description;
      }
      return scope;
    },
    async fetch() {
      try {
        this.fetching = true;
        const res = await axios.get(`${this.endpoint}`);
        this.items = res.data;
      } catch (e) {
        alert('取得に失敗しました');
      } finally {
        this.fetching = false;
      }
    },
    async handleDelete(item) {
      if (confirm("削除しますか？")) {
        try {
          await axios.delete(`${this.endpoint}/${item.id}`);
          this.fetch();
        } catch (e) {
          alert('削除に失敗しました');
        }
      }
    },
    handleCreate() {
      this.setItem();
      this.$bvModal.show(`modal-${this.modal}-editor`);
    },
    handleEdit(item) {
      this.setItem(item);
      this.$bvModal.show(`modal-${this.modal}-editor`);
    },
    async handleUpdateOrStore() {
      try {
        if (this.item.id) {
          await axios.put(`${this.endpoint}/${this.item.id}`, this.item);
        } else {
          const res = await axios.post(`${this.endpoint}`, this.item);
          this.onCreated(res.data);
        }
        this.$bvModal.close(`modal-${this.modal}-editor`);
        this.setItem();
        this.fetch();
      } catch (e) {
        this.handleError(e.response)
      }
    },
    onCreated(data) { },
    handleError(res) {
      if (!res) {
        return alert('通信エラーが発生しました。');
      }
      switch (res.status) {
        case 401:
          return alert('認証に失敗しました。');
        case 403:
          return alert(res?.data?.message || '操作を実行できませんでした。');
        case 404:
          return alert('データが見つかりませんでした。');
        case 419:
          return alert('ページの有効期限が切れました。ページを再読み込みしてから再度操作してください。');
        case 422:
          return alert(`入力データを確認してください。\n${Object.entries(res.data.errors).map(([label, message]) => message).join("\n")}`);
        case 429:
          return alert('リクエスト頻度制限により実行できませんでした。');
      }
      return alert(res?.data?.message || 'エラーが発生しました');
    }
  }
};
