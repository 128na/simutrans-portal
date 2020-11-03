export const editor = {
  data() {
    return {
      copy: null,
      has_changed: false,
    };
  },
  watch: {
    copy: {
      deep: true,
      handler(value) {
        if (!this.has_changed) {
          const original = this.getOriginal();
          const changed = JSON.stringify(original) !== JSON.stringify(value);
          if (changed) {
            this.setUnloadDialog();
          }
        }
      }
    }
  },
  methods: {
    setCopy(item) {
      this.copy = JSON.parse(JSON.stringify(item));
    },
    setUnloadDialog() {
      this.has_changed = true;
      window.addEventListener('beforeunload', this._unloadDialogEvent);
    },
    unsetUnloadDialog() {
      this.has_changed = false;
      window.removeEventListener('beforeunload', this._unloadDialogEvent);
    },
    _unloadDialogEvent(e) {
      e.preventDefault();
      e.returnValue = '保存せずに移動しますか？';
    }
  },
  beforeRouteLeave(to, from, next) {
    if (this.has_changed) {
      return next(window.confirm('保存せずに移動しますか？'));
    }
    return next();
  }
};
