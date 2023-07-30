<script lang="ts" setup>
import { Question, QuestionItem } from '@/assets/data/interfaces';
import TestHeader from '@/components/TestHeader.vue';
import TestLanding from '@/components/TestLanding.vue';
import TestQuestion from '@/components/TestQuestion.vue';
import { useGetIndexOfFirstItemWithoutProp } from '@/composables';
import router from '@/routes';
import { useLoaderStore, useTestsStore } from '@/stores';
import axios, { AxiosRequestConfig } from 'axios';
import { storeToRefs } from 'pinia';
import { Ref, ref } from 'vue';
import { useRoute } from 'vue-router';

const testsStore = useTestsStore();
const loader = useLoaderStore();
loader.setLoader();

const render404 = () => {
	router.push('404');
};

/**
 * Fetches the test based on the token in the query
 * @param token the token in the query string
 */
const fetchTest = async (token: string) => {
	const params: AxiosRequestConfig = { params: { token } };

	try {
		await testsStore.fetch(params);

		// creates the pages wit one Questionnaire per pages
		test.value.questions.forEach(question => {
			pages.value.push({ question });
			// sets the first non-completed Questionnaire as the
			active.value = useGetIndexOfFirstItemWithoutProp(
				test.value.questions,
				'completed'
			);
			// TODO RESET ANSWERS IF MORE THAN X HOURS HAVE PASSED

			// TODO RESET ANSWERS IF MORE THAN X HOURS HAVE PASSED
		});
	} catch (err) {
		if (axios.isAxiosError(err)) {
			// axios returns an error if the test is already complete, in that case we just redirect to page 404
			console.warn(err.response?.data.error);
			render404();
		} else console.error(err);
	} finally {
		loader.unsetLoader();
	}
};

const route = useRoute();
let token: string;

if (route.query.token) {
	token = route.query.token as string;
	fetchTest(token);
} else render404();

const { test } = storeToRefs(testsStore);
const showLanding = ref(true);
const active = ref(0);
const isCompleted = ref(false);

interface Page {
	question: Question;
}

const pages: Ref<Page[]> = ref([]);

/**
 * Handle the patient's answer and saves it to the database
 */
const handleAnswer = (itemId: number, answer: number): void => {
	const itemToUpdate = test.value.questions[active.value].items.find(
		({ id }) => id === itemId
	) as QuestionItem;

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
			<!-- AT TEST COMPLETION -->
			<div
				v-else-if="isCompleted"
				class="text-center pt-5"
			>
				<h2>Grazie per aver completato il test</h2>
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
