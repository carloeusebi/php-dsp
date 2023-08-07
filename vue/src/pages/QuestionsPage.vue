<script lang="ts" setup>
import { storeToRefs } from 'pinia';
import { computed, ref } from 'vue';

import AppSearchbar from '@/components/AppSearchbar.vue';
import AppAlert from '@/components/AppAlert.vue';
import QuestionsRow from '@/components/questions/QuestionsRow.vue';
import QuestionCreate from '@/components/questions/QuestionCreate.vue';
import QuestionTags from '@/components/questions/QuestionTags.vue';

import { useQuestionsStore } from '@/stores';
import { useSearchFilter } from '@/composables';

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
			class="text-gray-700 hover:text-gray-800 font-medium rounded-md mb-5"
		>
			<font-awesome-icon
				:icon="['fas', 'circle-chevron-left']"
				class="me-2"
			/>
			Indietro
		</button>
		<div class="relative flex items-center gap-6">
			<AppSearchbar @key-press="handleSearchbarKeypress" />
			<QuestionTags />
		</div>
		<div class="flex justify-end">
			<QuestionCreate />
		</div>

		<!-- QUESTIONS LIST -->
		<ul
			v-if="filteredQuestions.length > 0"
			class="px-5 lg:px-0"
		>
			<QuestionsRow
				v-for="question in filteredQuestions"
				:question="{ ...question }"
				:key="question.id || question.question"
			/>
		</ul>
		<AppAlert
			v-else
			:show="true"
			title="Ops!"
		>
			Nessun questionario trovato!
		</AppAlert>
	</section>
</template>
