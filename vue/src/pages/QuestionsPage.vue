<script lang="ts" setup>
import { useQuestionsStore } from '@/stores';
import { storeToRefs } from 'pinia';
import { computed, ref } from 'vue';

import AppSearchbar from '@/components/AppSearchbar.vue';
import QuestionsRow from '@/components/QuestionsRow.vue';
import AppAlert from '@/components/AppAlert.vue';
import { useSearchFilter } from '@/composables';
import QuestionCreate from '@/components/QuestionCreate.vue';

const questionsStore = useQuestionsStore();
const { questions } = storeToRefs(questionsStore);
const searchWord = ref('');

const handleSearchbarKeypress = (word: string) => (searchWord.value = word.toLowerCase());

const filteredQuestions = computed(() => {
	if (questions.value === null) return [];
	return useSearchFilter(questions.value, searchWord.value, ['question']);
});
</script>

<template>
	<section class="relative container mx-auto mt-6 p-2 lg:p-6">
		<button
			@click="$router.go(-1)"
			class="text-gray-700 hover:text-gray-800 font-medium rounded-md mb-5">
			<font-awesome-icon
				:icon="['fas', 'circle-chevron-left']"
				class="me-2" />
			Indietro
		</button>
		<div class="relative flex mb-10">
			<AppSearchbar @key-press="handleSearchbarKeypress" />
			<QuestionCreate />
		</div>

		<!-- QUESTIONS LIST -->
		<ul
			v-if="filteredQuestions.length > 0"
			class="px-5 lg:px-0">
			<QuestionsRow
				v-for="question in filteredQuestions"
				:question="{ ...question }"
				:key="question.id || question.question" />
		</ul>
		<AppAlert
			v-else
			:show="true"
			title="Ops!">
			Nessun questionario trovato!
		</AppAlert>
	</section>
</template>
