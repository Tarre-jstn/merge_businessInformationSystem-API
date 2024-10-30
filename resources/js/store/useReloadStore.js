import { defineStore } from 'pinia';

export const useReloadStore = defineStore('reload', {
  state: () => ({
    reloadOnce: 0, // Initialize reloadOnce to 0
  }),
  actions: {
    setReloadOnce(value) {
      this.reloadOnce = value;
    },
  },
});
