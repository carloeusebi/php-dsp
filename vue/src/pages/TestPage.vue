<script lang="ts" setup>
import { Question, QuestionItem } from '@/assets/data/interfaces';
import TestHeader from '@/components/TestHeader.vue';
import TestLanding from '@/components/TestLanding.vue';
import TestQuestion from '@/components/TestQuestion.vue';
import { useGetFirstItemWithoutPropIndex } from '@/composables/_useGetFirstItemWithoutPropIndex';
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

		test.value.survey.questions.forEach(question => {
			pages.value.push({ question });
			active.value = useGetFirstItemWithoutPropIndex(
				test.value.survey.questions,
				'completed'
			);
		});
	} catch (err) {
		if (axios.isAxiosError(err)) {
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

const handleAnswer = (itemId: number, answer: number): void => {
	const itemToUpdate = test.value.survey.questions[active.value].items.find(
		({ id }) => id === itemId
	) as QuestionItem;

	itemToUpdate.answer = answer;

	testsStore.save(test.value.survey);
};

/**
 * Saves the survey and uses the store the make an ajax call
 */
const handleQuestionComplete = () => {
	const isLastQuestion = () =>
		active.value === test.value.survey.questions.length - 1;

	test.value.survey.questions[active.value].completed = true;
	if (isLastQuestion()) {
		test.value.survey.completed = true;
		isCompleted.value = true;
	} else {
		active.value++;
	}
	testsStore.save(test.value.survey);
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
