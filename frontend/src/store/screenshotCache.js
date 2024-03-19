import { defineStore } from 'pinia';

/**
 * フロント用記事キャッシュ
 */
export const useScreenshotCache = defineStore('screenshotCache', {
  state: () => ({ cached: [] }),
  getters: {
    hasCache: (state) => (id) => {
      id = Number(id);
      return state.cached.findIndex((c) => c.id === id) !== -1;
    },
    getCache: (state) => (id) => {
      id = Number(id);
      return state.cached.find((c) => c.id === id);
    },
  },
  actions: {
    addCache(screenshot) {
      const index = this.cached.findIndex((c) => c.id === screenshot.id);
      if (index === -1) {
        this.cached.push(screenshot);
      } else {
        this.cached.splice(index, 1, screenshot);
      }
    },
    addCaches(screenshots) {
      screenshots.map((s) => this.addCache(s));
    },
  },
});
