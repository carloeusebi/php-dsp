import { defineStore } from 'pinia';

export const useLoaderStore = defineStore('loader', {
	state: () => ({ isLoading: false }),
	actions: {
		setLoader() {
			this.isLoading = true;
		},
		unsetLoader() {
			this.isLoading = false;
		},
	},
});
