<script lang="ts" setup>
import { Ref, computed, ref } from 'vue';
import axios from 'axios';

import AppButtonBlank from '@/components/AppButtonBlank.vue';
import AppButton from '@/components/AppButton.vue';
import AppModal from '@/components/AppModal.vue';
import AppInputElement from '@/components/AppInputElement.vue';
import AppAlert from '@/components/AppAlert.vue';

import { useLoaderStore, useQuestionsStore } from '@/stores';
import { emptyQuestion, questionTypes } from '@/assets/data/data';
import { Errors, Question } from '@/assets/data/interfaces';

const questionStore = useQuestionsStore();
const labels = questionStore.getLabels;

const showModal = ref(false);
const newQuestion = ref({ ...emptyQuestion });
const types = ref(questionTypes);
const errors: Ref<Errors> = ref({});

const errorsStr = computed(() => {
	const keys = Object.keys(errors.value);
	return keys.reduce((str, key) => (str += `${errors.value[key]}<br>`), '');
});

const create = async () => {
	const loader = useLoaderStore();

	loader.setLoader();
	errors.value = {};

	newQuestion.value.legend = [];
	newQuestion.value.items = [];
	newQuestion.value.variables = [];

	try {
		await questionStore.save({ ...(newQuestion.value as Question) });
		showModal.value = false;
		newQuestion.value = { ...emptyQuestion };
	} catch (err) {
		if (axios.isAxiosError(err)) errors.value = err.response?.data;
		else console.error(err);
	} finally {
		loader.unsetLoader();
	}
};
</script>

<template>
	<AppButtonBlank
		@click="showModal = true"
		color="blue"
		icon="plus"
		label="Aggiungi un nuovo questionario"
	/>

	<AppModal
		:open="showModal"
		@close="showModal = false"
	>
		<template v-slot:content>
			<h2>Crea nuovo Questionario</h2>
			<hr class="my-5" />

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
				id="question-create"
				@submit.prevent="create"
			>
				<div class="grid md:grid-cols-3 md:gap-6 mb-6">
					<!-- TITLE -->
					<div class="md:col-span-2 mb-4">
						<AppInputElement
							v-model="newQuestion.question"
							:label="labels.question"
						/>
					</div>

					<!-- TYPE -->
					<div class="md:col-span-1">
						<AppInputElement
							v-model="newQuestion.type"
							:label="labels.type"
							type="select"
						>
							<option
								v-for="t in types"
								:key="t"
								:value="t"
							>
								{{ t }}
							</option>
						</AppInputElement>
					</div>
				</div>

				<!-- DESCRIPTION TEXTAREA -->
				<div class="relative mb-6">
					<AppInputElement
						v-model="newQuestion.description"
						:label="labels.description"
						type="textarea"
					/>
				</div>
			</form>
		</template>
		<template v-slot:button>
			<AppButton form="question-create">Crea</AppButton>
		</template>
	</AppModal>
</template>
