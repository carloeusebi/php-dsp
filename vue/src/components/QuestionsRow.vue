<script lang="ts" setup>
import { computed, ref, nextTick, Ref } from 'vue';

import { Errors, Question } from '@/assets/data/interfaces';
import { useQuestionsStore } from '@/stores';
import { useSaveToStore } from '@/composables';

import AppModal from './AppModal.vue';
import AppButton from './AppButton.vue';
import AppAlert from './AppAlert.vue';
import QuestionForm from './QuestionForm.vue';
import QuestionDelete from './QuestionDelete.vue';

interface Props {
	question: Question;
}

const props = defineProps<Props>();

//refs
const questionRef = ref({ ...props.question });
const showModal = ref(false);
const modalComponent = ref<InstanceType<typeof AppModal> | null>(null);

const errors: Ref<Errors> = ref({});

const errorsStr = computed(() => {
	const keys = Object.keys(errors.value);
	return keys.reduce((str, key) => (str += `${errors.value[key]}<br>`), '');
});

const scrollToBottom = () => {
	// scroll to bottom
	nextTick(() => {
		const modal = modalComponent.value?.$refs.modal as HTMLTemplateElement;
		if (!modalComponent.value) return;
		modal.scrollTo(0, modal.scrollHeight);
	});
};

/***************************************** */
/*********** SAVE ************************ */
/**
 * Saves the updated question to the store and db
 */
const saveQuestion = async () => {
	/**
	 * Stretches or Truncate the legend based on the Questionnaire's type
	 * @param legend The Questionnaire Legend
	 * @param questionType The Questionnaire Type
	 * @returns The stretched or truncated legend
	 */
	const getCorrectNumberOfLegendsBasedOnQuestionType = (
		legend: Question['legend'],
		questionType: Question['type']
	): Question['legend'] => {
		/**
		 * Given the type of the question, it returns the number of total answers
		 * @param type the type of the answer
		 * @returns the total number of answers of the questionnaire
		 */
		const getNumberOfAnswers = (type: Question['type']): number => {
			const lowestAnswer = parseInt(type.at(0) as string);
			const highestAnswer = parseInt(type.at(-1) as string);
			return highestAnswer - lowestAnswer + 1;
		};

		const numberOfAnswers = getNumberOfAnswers(questionType);
		return legend.slice(0, numberOfAnswers);
	};

	errors.value = {};

	questionRef.value.legend = getCorrectNumberOfLegendsBasedOnQuestionType(
		questionRef.value.legend,
		questionRef.value.type
	);

	/**
	 * Attempt to save the Question
	 */
	const questionsStore = useQuestionsStore();
	errors.value = await useSaveToStore(questionRef.value, questionsStore);
	if (!errorsStr.value) closeModal();
};

const closeModal = () => {
	showModal.value = false;
	setTimeout(() => {
		questionRef.value = { ...props.question };
	}, 250);
};
</script>

<template>
	<li
		@click="showModal = true"
		class="py-4 border-y border-gray-200 bg-gray-50 hover:scale-110 transition duration-300 cursor-pointer"
	>
		<span class="font-semibold me-2">{{ question.question }}:</span>
		<span class="text-sm hidden md:inline">{{ question.description }}</span>
	</li>

	<AppModal
		:open="showModal"
		@close="closeModal"
		dimensions="sm:max-w-6xl"
		ref="modalComponent"
	>
		<template v-slot:content>
			<div class="flex justify-between items-center">
				<h2 class="text-2xl font-medium">{{ questionRef.question }}</h2>
				<div class="flex flex-col md:flex-row gap-y-1">
					<QuestionDelete :to-delete-question="questionRef" />
					<AppButton
						form="question-form"
						class="ms-2"
					>
						Salva</AppButton
					>
				</div>
			</div>
			<hr class="my-6" />

			<!-- ALERT -->
			<AppAlert
				:show="errorsStr.length > 0"
				type="warning"
				title="Attenzione"
				class="my-4"
				:transition="false"
			>
				<span v-html="errorsStr"></span>
			</AppAlert>

			<!-- FORM -->
			<form
				id="question-form"
				@submit.prevent="saveQuestion"
			>
				<QuestionForm
					@answer-added="scrollToBottom"
					:question="questionRef"
				/>
			</form>

			<!-- FORM END -->
		</template>
		<template v-slot:button>
			<AppButton form="question-form"> Salva le modifiche</AppButton>
		</template>
	</AppModal>
</template>
