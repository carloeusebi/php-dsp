<script lang="ts" setup>
import { Ref, computed, ref } from 'vue';
import draggable from 'vuedraggable';

import { Errors, Patient, Survey } from '@/assets/data/interfaces';
import { emptySurvey } from '@/assets/data/data';
import { useSaveToStore, useSort } from '@/composables';
import { usePatientsStore, useQuestionsStore, useSurveysStore } from '@/stores';

import AppButtonBlank from '@/components/AppButtonBlank.vue';
import AppModal from '@/components/AppModal.vue';
import AppInputElement from '@/components/AppInputElement.vue';
import AppButton from '@/components/AppButton.vue';
import AppAlert from '@/components/AppAlert.vue';

interface Props {
	patient?: Patient;
}

const props = defineProps<Props>();

if (props.patient) emptySurvey.patient_id = props.patient.id as number;

const showModal = ref(false);
const patients = useSort(usePatientsStore().getPatients, 'lname', 'down');
const questions = ref(useQuestionsStore().getQuestions);

const newSurvey: Ref<Survey> = ref({ ...emptySurvey });

const errors: Ref<Errors> = ref({});

const errorsStr = computed(() => {
	const keys = Object.keys(errors.value);
	return keys.reduce((str, key) => (str += `${errors.value[key]}<br>`), '');
});

/**
 * Checks for errors
 */
const handleFormSubmit = () => {
	// check for errors
	errors.value = {};

	if (!newSurvey.value.title) {
		errors.value['no-title'] = 'Il nome per il Sondaggio è obbligatorio';
	}

	if (!newSurvey.value.patient_id) {
		errors.value['no-patient'] = 'Nessun Paziente selezionato, il Paziente è obbligatorio';
	}

	const selectedQuestions = questions.value.filter(({ selected }) => selected);
	if (!selectedQuestions.length) {
		errors.value['no-questions'] = 'Nessun questionario selezionato, selezionarne almeno uno';
	}

	if (errorsStr.value) return;

	newSurvey.value.questions = [...selectedQuestions];
	saveSurvey();
};

/**
 * Let the store handle the saving
 */
const saveSurvey = async () => {
	const surveyStore = useSurveysStore();
	errors.value = await useSaveToStore(newSurvey.value, surveyStore);

	if (!errorsStr.value) {
		showModal.value = false;
		newSurvey.value = { ...emptySurvey };
		questions.value = questions.value.map(q => {
			q.selected = false;
			return q;
		});
	}
};
</script>

<template>
	<AppButtonBlank
		color="green"
		@click="showModal = true"
	>
		<font-awesome-icon
			:icon="['fas', 'plus']"
			class="fa-xl"
		/>
		<span class="hidden md:inline ms-3">Aggiungi nuovo sondaggio</span>
	</AppButtonBlank>

	<AppModal
		:id="'Create' + (patient?.id || -1)"
		dimensions="sm:max-w-7xl"
		:open="showModal"
		@close="showModal = false"
	>
		<template #content>
			<h2>
				Crea un nuovo sondaggio
				<span v-if="patient"> per {{ patient.fname }} {{ patient.lname }}</span>
			</h2>

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
			<hr class="my-8" />

			<!-- FORM -->
			<form
				@submit.prevent="handleFormSubmit"
				id="survey-create"
			>
				<!-- TITLE -->

				<AppInputElement
					class="mb-5"
					v-model.trim="newSurvey.title"
					label="Nome del sondaggio"
					:required="true"
				/>

				<!-- PATIENT -->

				<AppInputElement
					v-if="!patient"
					v-model="newSurvey.patient_id"
					type="select"
					label="Seleziona un paziente"
					:required="true"
				>
					<option
						v-for="patient in patients"
						:key="patient.id"
						:value="patient.id"
					>
						{{ patient.fname }} {{ patient.lname }}
					</option>
				</AppInputElement>

				<!-- QUESTIONS -->
				<p class="text-black my-5 text-xl">Seleziona i questionari da aggiungere al sondaggio e il loro ordine</p>

				<draggable
					item-key="id"
					tag="ul"
					v-model="questions"
					:animation="150"
					:delay="250"
					:delay-on-touch-only="true"
				>
					<template #item="{ element: question }">
						<li class="select-none">
							<!-- CHECKBOX -->
							<label class="container shrink">
								<input
									:id="question.id"
									:value="question.id"
									v-model="question.selected"
									type="checkbox"
									class="me-2 cursor-pointer"
								/>
								<span class="checkmark"></span>
							</label>
							<label
								:for="question.id"
								class="ms-7 py-1 inline-block md:text-lg cursor-pointer text-gray-700 hover:text-black transition-colors"
								>{{ question.question }}</label
							>
						</li>
					</template>
				</draggable>
			</form>
		</template>
		<template #button>
			<AppButton form="survey-create"> Crea </AppButton>
		</template>
	</AppModal>
</template>

<style lang="scss" scoped>
@use '@/assets/scss/checkbox';

label.container {
	bottom: 5px;
}
</style>
