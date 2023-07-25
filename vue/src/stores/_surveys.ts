import { Survey } from '@/assets/data/interfaces';
import { isAxiosError } from 'axios';
import { defineStore } from 'pinia';

const endpoint = '/surveys';

export const useSurveysStore = defineStore('surveys', {
	//state
	state: () => ({
		surveys: JSON.parse(localStorage.getItem('SURVEYS') as string) as Survey[],
	}),

	// getters
	getters: {
		getSurveys: (state): Survey[] => state.surveys,
	},

	//actions
	actions: {
		async fetch() {
			return this.axios.get(endpoint).then(res => this.load(res.data.list));
		},

		getById(id: string): Survey | undefined {
			return this.surveys.find(s => String(s.id) == id);
		},

		load(surveys: Survey[]) {
			this.surveys = surveys;
			localStorage.setItem('SURVEYS', JSON.stringify(surveys));
		},

		async save(survey: Survey) {
			return this.axios
				.post(endpoint, survey)
				.then(res => {
					const newSurvey = res.data.survey;

					const indexToUpdate = this.surveys.findIndex(
						({ id }) => id == newSurvey.id
					);

					if (indexToUpdate === -1) {
						this.surveys.push(newSurvey);
					} else {
						this.surveys[indexToUpdate] = newSurvey;
					}

					this.load(this.surveys);
				})
				.catch(e => {
					throw e;
				});
		},

		async delete(id: number) {
			return this.axios.delete(endpoint, { data: { id } }).then(() => {
				// delete surveys from local store
				const filteredSurveys = [...this.surveys.filter(s => s.id !== id)];
				this.load(filteredSurveys);
			});
		},
	},
});
