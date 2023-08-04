import { Survey } from '@/assets/data/interfaces';
import { deleteMixin, formatDateToIta, saveMixin } from '@/mixins';
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
		async fetch(id?: number) {
			const params = {
				id,
			};
			return this.axios.get(endpoint, { params }).then(res => this.load(res.data.list));
		},

		fetchById(id: string): Survey | undefined {
			return this.surveys.find(s => String(s.id) == id);
		},

		load(surveys: Survey[]) {
			this.surveys = surveys.map(s => {
				s.patient_name = `${s?.fname} ${s.lname}`;
				if (s.created_at) {
					s.created_at = formatDateToIta(s.created_at);
				}
				if (s.last_update) {
					s.last_update = formatDateToIta(s.last_update);
				}

				return s;
			});
			localStorage.setItem('SURVEYS', JSON.stringify(surveys));
		},

		/**
		 * Saves the survey to the db and updates the local store with the new data
		 * @param survey The survey to be saved in the server request
		 */
		async save(survey: Survey): Promise<void> {
			return saveMixin(this, endpoint, survey, this.surveys, this.load);
		},

		/**
		 * Deletes surveys from the db and updates the local store after deletion
		 * @param id The ID of the survey to be deleted
		 */
		async delete(id: number) {
			await deleteMixin(this, endpoint, id, this.surveys, this.load);
		},
	},
});
