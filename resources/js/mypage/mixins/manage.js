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
      if (this.item.id) {
        try {
          await axios.put(`${this.endpoint}/${this.item.id}`, this.item);
          return this.fetch();
        } catch (e) {
          return alert("更新に失敗しました。");
        }
      }

      try {
        const res = await axios.post(`${this.endpoint}`, this.item);
        this.onCreated(res.data);
        this.setItem();
        this.fetch();
      } catch (e) {
        return alert("作成に失敗しました。");
      }
    },
    onCreated(data) { }
  }
};
