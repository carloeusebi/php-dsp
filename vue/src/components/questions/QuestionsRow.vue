<script lang="ts" setup>
import { computed, ref, Ref } from 'vue';

import { Errors, Question, Tag } from '@/assets/data/interfaces';
import { useQuestionsStore, useTagsStore } from '@/stores';
import { useSaveToStore, useScrollTo } from '@/composables';

import AppModal from '@/components/AppModal.vue';
import AppButton from '@/components/AppButton.vue';
import AppAlert from '@/components/AppAlert.vue';
import QuestionForm from './QuestionForm.vue';
import QuestionDelete from './QuestionDelete.vue';
import QuestionTags from './QuestionTags.vue';

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

/**
 * Scrolls the modal using the composable `useScrollTo`
 * @param where Where to scroll in pixel, 0 top, -1 bottom.
 */
const scrollTo = (where: number) => {
	const modal = modalComponent.value?.$refs.modal as HTMLTemplateElement;
	if (!modalComponent.value) return;
	useScrollTo(modal, where);
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
			if (type === 'EDI') return 6;

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
	else useScrollTo(modalComponent.value?.$refs.modal as HTMLTemplateElement, 0);
};

const closeModal = () => {
	showModal.value = false;
};

// TAGS
// ***********************************

/**
 * Updates the selected tags for a question based on the new tag IDs provided.
 * @param {number[]} newValue - An array of tag IDs to set as selected for the question.
 */
const handleTagSelectionChange = (newValue: number[]) => {
	questionRef.value.tags = [];
	newValue.forEach(tagId => {
		const tagToAdd = useTagsStore().getById(tagId);
		if (tagToAdd) {
			questionRef.value.tags = [...(questionRef.value.tags as Tag[]), tagToAdd];
		}
	});
};
</script>

<template>
	<li
		@click="showModal = true"
		class="py-4 border-y border-gray-200 bg-gray-50 hover:scale-[1.01] transition duration-300 cursor-pointer"
	>
		<div class="flex flex-col sm:flex-row items-start sm:items-center gap-1">
			<!-- title -->
			<div class="font-semibold me-2">{{ question.question }}</div>
			<!-- tags -->
			<ul class="flex gap-2">
				<li
					v-for="tag in question.tags"
					:key="tag.id"
					:style="`background-color: ${tag.color}10; color: ${tag.color}; border: 1px solid ${tag.color}50`"
					class="inline-flex items-center rounded-md px-2 py-[2px] text-xs font-medium"
				>
					{{ tag.tag }}
				</li>
			</ul>
		</div>
		<!-- description -->
		<p class="text-sm hidden md:inline">{{ question.description }}</p>
	</li>

	<AppModal
		:open="showModal"
		@close="closeModal"
		dimensions="sm:max-w-6xl"
		ref="modalComponent"
	>
		<template v-slot:content>
			<header class="flex flex-col lg:flex-row lg:justify-between items-stretch gap-2">
				<h2 class="text-2xl font-medium self-start order-1">{{ questionRef.question }}</h2>
				<!-- HEADER BUTTONS AND TITLE ^ -->
				<div class="flex flex-wrap w-full md:w-auto justify-center gap-y-1 gap-2 self-end lg:order-1">
					<!-- close button -->
					<button
						type="button"
						class="inline-flex grow md:grow-0 justify-center items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mt-0 sm:w-auto"
						@click="closeModal"
					>
						Chiudi
					</button>
					<!-- delete button -->
					<QuestionDelete :to-delete-question="questionRef" />
					<!-- tags -->
					<QuestionTags
						:starting-selection="questionRef.tags?.map(({ id }) => id)"
						@change-selection="handleTagSelectionChange"
					/>
					<!-- save button -->
					<AppButton form="question-form"> Salva</AppButton>
				</div>
			</header>
			<ul class="flex gap-2 min-h-[22px]">
				<li
					v-for="tag in questionRef.tags"
					:key="tag.id"
					:style="`background-color: ${tag.color}10; color: ${tag.color}; border: 1px solid ${tag.color}50`"
					class="inline-flex items-center rounded-md px-2 py-[2px] text-xs font-medium"
				>
					{{ tag.tag }}
				</li>
			</ul>
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
					@answer-added="scrollTo($event)"
					@variable-added="scrollTo($event)"
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
