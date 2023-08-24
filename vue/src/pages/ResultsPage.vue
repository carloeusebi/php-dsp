<script lang="ts" setup>
import { onMounted, ref } from 'vue';

import { Patient, Question, QuestionItemI, Survey } from '@/assets/data/interfaces';
import { useLoaderStore, usePatientsStore, useSurveysStore } from '@/stores';
import { useRoute } from 'vue-router';
import axiosInstance from '@/assets/axios';
import { isAxiosError } from 'axios';
import ResultsHeader from '@/components/ResultsHeader.vue';
import ResultsPatient from '@/components/ResultsPatient.vue';

interface PrintableQuestion extends Question {
	printable: boolean;
}

const route = useRoute();
const surveyId = parseInt(route.params.id as string);
const loader = useLoaderStore();
const survey = ref<Survey | null>(null);
const patient = ref<Patient | null>(null);

/**
 * On mount fetches the Survey details, which are not stored locally
 */
onMounted(async () => {
	/**
	 * Fetches the Survey details, which are not stored locally.
	 * @param id The Survey ID to fetch
	 * @async
	 */
	const fetchSurveyQuestion = async (id: number): Promise<Survey | undefined> => {
		/**
		 * Adds the printable propriety to each questionnaire
		 * @param questions The questions to modify
		 */
		const makeQuestionsPrintable = (questions: Question[]): PrintableQuestion[] =>
			questions.map(q => ({ ...q, printable: true })) as PrintableQuestion[];

		loader.setLoader();
		const params = { id };
		try {
			const res = await axiosInstance.get('surveys', { params });
			const survey = res.data.list as Survey;
			survey.questions = makeQuestionsPrintable(survey.questions);
			return survey;
		} catch (err) {
			if (isAxiosError(err)) alert(err.response?.data);
			else alert(err);
		} finally {
			loader.unsetLoader();
		}
		return undefined;
	};

	survey.value = (await fetchSurveyQuestion(surveyId)) as Survey;
	patient.value = usePatientsStore().getById(survey.value?.patient_id) as Patient;
	checkboxes.value = fillCheckboxes();
});

const editMode = ref(false);

const min = (question: Question): number => (question.type === 'EDI' ? 0 : parseInt(question.type.at(0) as string));
const itemValue = (question: Question, n: number): number => min(question) + n;

const checkboxes = ref<Array<boolean[]>>([]);

const fillCheckboxes = () => {
	const checkboxes: Array<boolean[]> = [];
	if (!survey.value) return checkboxes;
	survey.value.questions.forEach(question => {
		const legends = [];
		for (let i = 0; i < question.legend.length; i++) {
			legends.push(false);
		}
		checkboxes.push(legends);
	});
	return checkboxes;
};

/**
 * Updates an answer
 * @param questionId the question where the answer to update is
 * @param itemId the item to update
 * @param answer the new answer
 */
const changeAnswer = (questionId: number | undefined, itemId: number, answer: number): void => {
	// return if we are not in question mode
	if (!editMode.value || !survey.value) return;
	const questionToUpdate = survey.value.questions.find(({ id }) => id === questionId);
	const itemToUpdate = questionToUpdate?.items.find(({ id }) => id === itemId) as QuestionItemI;
	itemToUpdate.answer = answer;
};

/**
 * Saves the survey and uses the store to make an ajax call
 */
const saveUpdates = async () => {
	if (!survey.value) return;
	loader.setLoader();
	try {
		await useSurveysStore().save({ ...survey.value });
	} catch (err) {
		if (isAxiosError(err) && err.response?.status === 403)
			alert("Devi aver effettuato l'accesso per vedere questa pagina");
		else console.error(err);
	} finally {
		loader.unsetLoader();
		editMode.value = false;
	}
};

/**
 * Handles the delete of a comment, prompts with a confirmation question and in case proceeds to delete the message
 */
const handleDeleteComment = (questionId: number, itemId: number) => {
	if (!survey.value) return;
	const question = survey.value.questions.find(({ id }) => questionId === id);
	const item = question?.items.find(({ id }) => itemId === id) as QuestionItemI;

	const proceed = confirm(`Sicuro di voler cancellare il commento\n"${item.comment}"\ndella domanda\n"${item.text}"?`);

	// if answer is negative return
	if (!proceed) return;

	item.comment = '';
	saveUpdates();
};
</script>

<template>
	<div class="bg-white min-h-screen">
		<div class="container max-w-6xl mx-auto p-6">
			<!-- HEADER -->
			<ResultsHeader :title="survey?.title" />

			<!-- PATIENT -->
			<ResultsPatient
				v-if="survey"
				:survey="survey"
			/>

			<!-- QUESTIONNAIRE -->
			<section
				v-for="(question, i) in (survey?.questions as PrintableQuestion[])"
				:key="question.id"
				:id="question.id.toString()"
				class="my-10 border-b pb-5"
				:class="{ 'edit-mode': editMode, 'non-printable': !question.printable }"
			>
				<div
					class="inline-flex items-center gap-3 cursor-pointer mb-5"
					@click="question.printable = !question.printable"
				>
					<h2 class="mb-0">
						{{ question.question }}
					</h2>
					<font-awesome-icon
						:icon="['fas', question.printable ? 'eye' : 'eye-slash']"
						size="lg"
						class="text-gray-400"
					/>
				</div>
				<p class="mb-5">{{ question.description }}</p>
				<!-- LEGEND -->
				<div
					v-if="question.type !== 'MUL'"
					class="border border-black my-5 p-2 grid md:grid-cols-2"
				>
					<div
						v-for="(legend, j) in question.legend"
						:key="j"
						class="uppercase font-bold"
					>
						<!-- checkbox -->
						<label class="container shrink">
							<input
								v-model="checkboxes[i][j]"
								type="checkbox"
								class="cursor-pointer"
								:id="`cb-${i}-${j}`"
							/>
							<span class="checkmark"></span>
						</label>
						<!-- text -->
						<label
							:for="`cb-${i}-${j}`"
							class="ms-7 cursor-pointer"
							>{{ question.type === 'EDI' ? (j < 2 ? 0 : j - 2) : j + min(question) }} = {{ legend.legend }}</label
						>
					</div>
				</div>
				<!-- ITEMS -->
				<div>
					<div
						v-for="(item, itemNumber) in question.items"
						:key="item.id"
						class="grid grid-cols-6 relative"
					>
						<!-- question -->
						<div
							:class="[item.text ? 'col-span-4' : 'col-span-1']"
							class="p-2 border border-black flex justify-between"
						>
							{{ itemNumber + 1 }}. {{ item.text }}
						</div>
						<!-- ANSWERS -->
						<div :class="[item.text ? 'col-span-2' : 'col-span-5']">
							<!-- ! IF MUL -->
							<div
								v-if="item.multipleAnswers"
								class="flex h-full"
							>
								<div
									v-for="ans in item.multipleAnswers"
									:style="`flex-basis: calc(100% / ${item.multipleAnswers.length})`"
									:key="ans.id"
								>
									<div
										class="answer-cell border border-black flex-grow flex justify-center items-center h-full p-2"
										:class="{ 'bg-green-500': ans.points === item.answer }"
										@click="changeAnswer(question.id, item.id, ans.points)"
									>
										{{ ans.customAnswer }}
									</div>
								</div>
							</div>
							<!-- ! IF OTHER TYPE -->
							<div
								v-else
								class="flex h-full"
							>
								<!-- @vue-ignore -->
								<div
									v-for="(legend, n) in question.legend"
									:key="n"
									class="flex-grow h-full"
									:class="{ hidden: !checkboxes[i][n] && !checkboxes[i].every(cb => !cb) }"
								>
									<div
										class="answer-cell border border-black flex-grow flex justify-center items-center h-full"
										:class="{ 'bg-green-500': itemValue(question, n) === item.answer }"
										@click="changeAnswer(question.id, item.id, itemValue(question, n))"
									>
										{{ question.type === 'EDI' ? (n < 2 ? 0 : n - 2) : itemValue(question, n) }}
									</div>
								</div>
							</div>
						</div>
						<!-- COMMENTS -->
						<!-- comment for the digital version -->
						<div
							class="comment-container"
							v-if="item.comment"
						>
							<div class="flex">
								<font-awesome-icon
									:icon="['far', 'comment-dots']"
									size="xl"
									class="z-0 p-2"
								/>
							</div>
							<div class="comment flex">
								<!-- comment delete button -->
								<span class="grow">{{ item.comment }}</span>
								<font-awesome-icon
									@click="handleDeleteComment(question.id, item.id)"
									:icon="['fas', 'trash-can']"
									size="sm"
									class="ms-1 text-red-500 z-0 self-start cursor-pointer p-2"
								/>
							</div>
						</div>
						<!-- comment for the print version -->
						<div
							v-if="item.comment"
							class="print-comment"
						>
							*{{ item.comment }}
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>

	<!-- EDIT BUTTON -->
	<div
		class="edit-button"
		:class="[editMode ? 'bg-red-800 hover:bg-red-900' : 'bg-blue-800 hover:bg-blue-900']"
		@click="editMode = !editMode"
	>
		<font-awesome-icon
			:icon="['fas', 'pen-to-square']"
			size="2xl"
		/>
	</div>

	<!-- SAVE BUTTON -->
	<div
		v-if="editMode"
		@click="saveUpdates"
		class="save-button bg-green-800 hover:bg-green-900"
	>
		<font-awesome-icon
			:icon="['fas', 'floppy-disk']"
			size="2xl"
		/>
	</div>

	<!-- EDIT ALERT -->
	<div
		v-if="editMode"
		class="edit-mode-alert text-center text-xl md:text-4xl text-red-600 font-extrabold top-12 md:top-32 w-auto uppercase"
	>
		Modalità modifica attiva
	</div>
</template>

<style lang="scss" scoped>
@use '@/assets/scss/checkbox';
.checkmark {
	top: 3px;
}

* {
	-webkit-print-color-adjust: exact;
	print-color-adjust: exact;
}

.comment-container {
	position: absolute;
	top: 50%;
	left: 100.5%;
	transform: translateY(-50%);
	z-index: 1;

	&:hover .comment {
		display: flex;
	}
}

.comment {
	position: absolute;
	justify-content: center;
	align-items: center;
	display: none;
	background-color: #fff;
	padding: 1rem 2rem;
	box-shadow: 0 0 10px 2px black;
	z-index: 10;
	border-radius: 20px;
	right: 5px;
	min-width: max-content;
	max-width: 75vw;
}

.print-comment {
	display: none;
	text-align: right;
	width: 700px;
}

.edit-button,
.save-button {
	position: fixed;
	left: 2rem;

	box-shadow: 0 0 20px 2px #888;
	color: white;
	border-radius: 50%;
	width: 75px;
	aspect-ratio: 1;

	display: flex;
	justify-content: center;
	align-items: center;

	cursor: pointer;
}

.edit-button {
	bottom: 2rem;
}

.save-button {
	bottom: 7.5rem;
}

.edit-mode-alert {
	position: fixed;
	left: 50%;
	transform: translateX(-50%);

	text-shadow: 0 0 10px #888;
	user-select: none;
}

.edit-mode .answer-cell {
	cursor: pointer;
}

// PRINT MEDIA

@media print {
	section.non-printable {
		display: none;
	}
	.print-comment {
		display: block;
	}

	.fa-comment-dots {
		display: none;
	}

	.fa-eye,
	.fa-eye-slash {
		display: none;
	}

	.edit-button {
		display: none;
	}

	label.container {
		display: none;
	}
}
</style>
