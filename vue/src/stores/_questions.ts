import { Question } from '@/assets/data/interfaces';
import { deleteMixin, saveMixin } from '@/mixins';
import { defineStore } from 'pinia';

const endpoint = '/questions';

export const useQuestionsStore = defineStore('questions', {
	//state
	state: () => ({
		questions: JSON.parse(localStorage.getItem('QUESTIONS') as string) as Question[],
		labels: JSON.parse(localStorage.getItem('QUESTION_LABELS') as string) as Question,
	}),

	//getters
	getters: {
		getQuestions: (state): Question[] => state.questions,
		getLabels: (state): Question => state.labels,
	},

	//actions
	actions: {
		fetch() {
			this.axios.get(endpoint).then(res => {
				this.load(res.data);
			});
		},

		/**
		 * Loads bot labels and questions list received from the ajax call to the db
		 * @param questions An object with labels and questions list
		 */
		load(questionsAndLabels: QuestionsAndLabels) {
			this.loadQuestions(questionsAndLabels.list);
			this.loadLabels(questionsAndLabels.labels);
		},

		/**
		 * Load the questions list
		 * @param questions the questions's list
		 */
		loadQuestions(questions: Question[]) {
			questions.forEach(q => {
				if (!q.items) q.items = [];
				if (!q.legend) q.legend = [];
				if (!q.variables) q.variables = [];
			});
			this.questions = questions;
			localStorage.setItem('QUESTIONS', JSON.stringify(questions));
		},

		/**
		 * Loads the question's labels
		 * @param labels the user friendly labels in italian
		 */
		loadLabels(labels: Question) {
			this.labels = labels;
			localStorage.setItem('QUESTION_LABELS', JSON.stringify(labels));
		},

		/**
		 * Saves data to the db and updates the local store with the new data
		 * @param question The question to be saved
		 */
		async save(question: Question): Promise<void> {
			return await saveMixin(this, endpoint, question, this.questions, this.loadQuestions).catch(e => {
				throw e;
			});
		},

		/**
		 * Deletes data from the db and updates the local store after deletion
		 * @param id The ID of the item to delete
		 */
		async delete(id: number) {
			await deleteMixin(this, endpoint, id, this.questions, this.loadQuestions);
		},
	},
});

export interface QuestionsAndLabels {
	list: Question[];
	labels: Question;
}
