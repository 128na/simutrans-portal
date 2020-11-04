const dialog_message = '保存せずに移動しますか？';
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
    },
  },
  methods: {
    setCopy(item) {
      if (item) {
        this.copy = JSON.parse(JSON.stringify(item));
      }
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
      e.returnValue = dialog_message;
    },
  },
  /**
   * ページ遷移時に呼ばれる
   */
  beforeRouteLeave(to, from, next) {
    if (this.has_changed) {
      if (window.confirm(dialog_message)) {
        this.unsetUnloadDialog();
        return next();
      } else {
        return next(false);
      }
    }
    return next();
  },
  /**
   * パラメーター変化時（post_type）に呼ばれる
   */
  beforeRouteUpdate(to, from, next) {
    if (this.has_changed) {
      if (window.confirm(dialog_message)) {
        this.unsetUnloadDialog();
        return next();
      } else {
        return next(false);
      }
    }
    return next();
  },
};
