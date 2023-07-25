<script lang="ts" setup>
import { Errors, Question } from '@/assets/data/interfaces';
import AppModal from './AppModal.vue';
import { computed, ref, nextTick, Ref } from 'vue';
import { useLoaderStore, useQuestionsStore } from '@/stores';
import AppButton from './AppButton.vue';
import AppAlert from './AppAlert.vue';
import QuestionForm from './QuestionForm.vue';
import QuestionDelete from './QuestionDelete.vue';
import axios from 'axios';

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
	const questionsStore = useQuestionsStore();
	const loader = useLoaderStore();
	loader.setLoader();

	const maxAnswers = parseInt(questionRef.value.type.at(-1) as string);

	// cuts longer legends
	questionRef.value.legend = questionRef.value.legend.slice(0, maxAnswers);

	//spread the question because on the other side we stringify its content before sending it via ajax call, if we don't spread it we modify the object on this side too
	try {
		await questionsStore.save({ ...questionRef.value });
		closeModal();
	} catch (err) {
		if (axios.isAxiosError(err)) errors.value = err.response?.data;
		else console.error(err);
	} finally {
		loader.unsetLoader();
	}
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
