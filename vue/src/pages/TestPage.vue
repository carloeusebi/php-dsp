<script lang="ts" setup>
import axios, { AxiosRequestConfig } from 'axios';
import { storeToRefs } from 'pinia';
import { Ref, ref } from 'vue';
import { useRoute } from 'vue-router';

import TestHeader from '@/components/tests/TestHeader.vue';
import TestLanding from '@/components/tests/TestLanding.vue';
import TestQuestion from '@/components/tests/TestQuestion.vue';
import TestForm from '@/components/tests/TestForm.vue';

import router from '@/routes';
import { Question, QuestionItemI } from '@/assets/data/interfaces';
import { useGetIndexOfFirstItemWithoutProp, useGetTimeDifferenceFromNow } from '@/composables';
import { useLoaderStore, useTestsStore } from '@/stores';

/**
 * The maximum time the patient has to complete the questionnaire
 */
const MINUTES_TO_COMPLETE_QUESTIONNAIRE = 120;

/**
 * Disable back button navigation, it suggests the patient to leave a comment explaining why he wanted to go back to change the old question
 */
const disableBackButton = () => {
	window.history.pushState(null, '', window.location.href);
	window.onpopstate = () => {
		window.history.pushState(null, '', window.location.href);
		alert(
			'Non puoi più modificare le risposte precedenti, se vuoi puoi lasciare un commento in cui spieghi perché volevi cambiare la risposta precedente.\nSe invece volevi uscire ti basta chiudere la scheda.'
		);
	};
};

/**
 * Resets the answers for a specific question.
 * @param index The index of the question in the 'questions' array of the 'test' object.
 */
const resetAnswers = (index: number) => {
	const items = test.value.questions[index].items;
	if (items[0].answer === undefined) return; // if the questionnaire doesn't contain any answer the rest of the code doesn't need to be executed
	const resettedItems = items.map(item => {
		delete item.answer;
		return item;
	});
	test.value.questions[index].items = [...resettedItems];
	testsStore.save(test.value);
	alert(
		`Sono passate più di due ore dalla tua ultima risposta. Le risposte del questionario "${test.value.questions[index].question}" sono state azzerate.`
	);
};

/**
 * Redirects to page not found
 */
const goTo404 = () => {
	router.push('404');
};

/**
 * Fetches the test based on the token in the query
 * @param token the token in the query string
 */
const fetchTest = async (token: string) => {
	const params: AxiosRequestConfig = { params: { token } };
	loader.setLoader();

	try {
		await testsStore.fetch(params);

		// creates the pages wit one Questionnaire per pages
		test.value.questions.forEach(question => {
			pages.value.push({ question });
		});

		showForm.value = test.value.last_update === null;

		// sets the first non-completed Questionnaire as the active one
		active.value = useGetIndexOfFirstItemWithoutProp(test.value.questions, 'completed');

		// if more than 120 minutes have passed between answer, it resets the questionnaire
		if (test.value.last_update) {
			const minutesSinceLastAnswer = useGetTimeDifferenceFromNow(test.value.last_update as string, 'minutes');
			if (minutesSinceLastAnswer > MINUTES_TO_COMPLETE_QUESTIONNAIRE) {
				resetAnswers(active.value);
			}
		}
	} catch (err) {
		if (axios.isAxiosError(err)) {
			// axios returns an error if the test is already complete, in that case we just redirect to page 404
			console.warn(err.response?.data.error);
			goTo404();
		} else console.error(err);
	} finally {
		loader.unsetLoader();
	}
};

const testsStore = useTestsStore();
const loader = useLoaderStore();
disableBackButton();

const route = useRoute();
let token: string;

// reads the token
if (route.query.token) {
	token = route.query.token as string;
	fetchTest(token);
} else goTo404();

interface Page {
	question: Question;
}

const { test } = storeToRefs(testsStore);
const showLanding = ref(true);
const showForm = ref(false);

/**
 * The index of the active Questionnaire, the same of the active Page
 */
const active = ref(0);

/**
 * When true displays last page
 */
const isCompleted = ref(false);

const pages: Ref<Page[]> = ref([]);

/**
 * Handle the patient's answer and saves it to the database
 */
const handleAnswer = (itemId: number, answer: number): void => {
	const itemToUpdate = test.value.questions[active.value].items.find(({ id }) => id === itemId) as QuestionItemI;

	itemToUpdate.answer = answer;

	testsStore.save(test.value);
};

/**
 * Handles what happens when a questionnaires is completed
 */
const handleQuestionComplete = () => {
	const isLastQuestion = () => active.value === test.value.questions.length - 1;

	test.value.questions[active.value].completed = true;
	if (isLastQuestion()) {
		test.value.completed = true;
		isCompleted.value = true;
	} else {
		active.value++;
	}
	testsStore.save(test.value);
};
</script>

<template>
	<TestHeader />
	<div class="container px-5 mx-auto">
		<main>
			<!-- LANDING -->
			<TestLanding
				v-if="showLanding"
				@start="showLanding = false"
			/>
			<TestForm
				v-else-if="showForm"
				:patient-id="test.patient_id"
				:token="test.token"
				@form-submit="showForm = false"
			/>
			<!-- AT TEST COMPLETION -->
			<div
				v-else-if="isCompleted"
				class="text-center pt-5"
			>
				<h2>Grazie {{ test.fname }} per aver completato il test</h2>
				<p>Questo link non sarà più valido.</p>
			</div>
			<!-- TEST QUESTION -->
			<TestQuestion
				v-else
				:question="pages[active].question"
				:current="active + 1"
				:total="pages.length"
				@answered="handleAnswer"
				@question-complete="handleQuestionComplete"
			/>
		</main>
	</div>
</template>

<style scoped>
main {
	font-family: sans-serif;
}
</style>
