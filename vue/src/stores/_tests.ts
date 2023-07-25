import { Patient, Survey, Test } from '@/assets/data/interfaces';
import { AxiosRequestConfig, isAxiosError } from 'axios';
import { defineStore } from 'pinia';

const endpoint = '/tests';

export const useTestsStore = defineStore('tests', {
	// state
	state: () => ({
		test: JSON.parse(localStorage.getItem('TEST') as string) as Test,
	}),

	// getters
	getters: {
		getSurvey: (state): Survey => state.test.survey,
		getPatient: (state): Patient => state.test.patient,
	},

	// actions
	actions: {
		/**
		 * Fetches the test from the server
		 * @param params param should be the token
		 */
		async fetch(params: AxiosRequestConfig) {
			return this.axios
				.get(endpoint, params)
				.then(({ data }) => {
					this.load(data);
				})
				.catch(err => {
					if (isAxiosError(err)) console.warn(err.response?.data);
					throw err;
				});
		},

		/**
		 * Loads the test in the store and local storage
		 * @param test The test to load
		 */
		load(test: Test) {
			this.test = test;
			localStorage.setItem('TEST', JSON.stringify(test));
		},

		async save(survey: Survey) {
			this.test.survey = survey;
			localStorage.setItem('TEST', JSON.stringify(this.test));

			return this.axios.post(endpoint, survey).catch(err => {
				if (isAxiosError(err)) console.warn(err.response?.data);
				throw err;
			});
		},
	},
});
