export const preview = {
  data() {
    return {
      preview_window: null
    };
  },
  methods: {
    async createPreview(html) {
      if (!this.preview_window || this.preview_window.closed) {
        this.preview_window = window.open();
      }
      this.preview_window.document.body.innerHTML = html;
    }
  }
};
