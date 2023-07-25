import { Question } from '@/assets/data/interfaces';
import { isAxiosError } from 'axios';
import { defineStore } from 'pinia';

const endpoint = '/questions';

export const useQuestionsStore = defineStore('questions', {
	//state
	state: () => ({
		questions: JSON.parse(
			localStorage.getItem('QUESTIONS') as string
		) as Question[],
		labels: JSON.parse(
			localStorage.getItem('QUESTION_LABELS') as string
		) as Question,
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

		async save(question: Question) {
			return this.axios
				.post(endpoint, question)
				.then(res => {
					const newQuestion = res.data.question;

					const indexToUpdate = this.questions.findIndex(
						({ id }) => id == newQuestion.id
					);

					if (indexToUpdate === -1) {
						// it is new question
						this.questions.push(newQuestion);
					} else {
						this.questions[indexToUpdate] = newQuestion;
					}

					this.loadQuestions(this.questions);
				})
				.catch(e => {
					throw e;
				});
		},

		async delete(id: number) {
			return this.axios.delete(endpoint, { data: { id } }).then(() => {
				//delete question from local store
				const filteredQuestions = [...this.questions.filter(q => q.id !== id)];
				this.loadQuestions(filteredQuestions);
			});
		},
	},
});

export interface QuestionsAndLabels {
	list: Question[];
	labels: Question;
}
