<script lang="ts" setup>
import { Ref, computed, ref } from 'vue';
import AppButtonBlank from './AppButtonBlank.vue';
import AppModal from './AppModal.vue';
import {
	useLoaderStore,
	usePatientsStore,
	useQuestionsStore,
	useSurveysStore,
} from '@/stores';
import AppInputElement from './AppInputElement.vue';
import AppButton from './AppButton.vue';
import AppAlert from './AppAlert.vue';
import { Errors, Patient, Survey } from '@/assets/data/interfaces';
import { emptySurvey } from '@/assets/data/data';
import draggable from 'vuedraggable';
import axios from 'axios';

interface Props {
	patient?: Patient;
}

const props = defineProps<Props>();

const showModal = ref(false);
const patients = usePatientsStore().getPatients;
const questions = ref(useQuestionsStore().getQuestions);

const newSurvey: Ref<Survey> = ref({ ...emptySurvey });
if (props.patient)
	newSurvey.value.patient_id = props.patient.id?.toString() as string;

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
		errors.value['no-patient'] =
			'Nessun Paziente selezionato, il Paziente è obbligatorio';
	}

	const selectedQuestions = questions.value.filter(({ selected }) => selected);
	if (!selectedQuestions.length) {
		errors.value['no-questions'] =
			'Nessun questionario selezionato, selezionarne almeno uno';
	}

	if (errorsStr.value) return;

	newSurvey.value.questions = [...selectedQuestions];
	saveSurvey();
};

/**
 * Let the store handle the saving
 */
const saveSurvey = async () => {
	const loader = useLoaderStore();
	loader.setLoader();

	try {
		await useSurveysStore().save({ ...newSurvey.value });

		showModal.value = false;
		newSurvey.value = { ...emptySurvey };
		questions.value = questions.value.map(q => {
			q.selected = false;
			return q;
		});
	} catch (err) {
		if (axios.isAxiosError(err)) {
			errors.value = err.response?.data;
		} else console.error(err);
	} finally {
		loader.unsetLoader();
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
				<p class="text-black my-5 text-xl">
					Seleziona i questionari da aggiungere al sondaggio e il loro ordine
				</p>

				<draggable
					item-key="id"
					tag="ul"
					v-model="questions"
					:animation="150"
					:delay="250"
					:delay-on-touch-only="true"
				>
					<template #item="{ element: question }">
						<li class="select-none flex items-center">
							<label>
								<input
									type="checkbox"
									:value="question.id"
									v-model="question.selected"
								/>
								<span
									class="max-w-sm ms-3 py-1 inline-block md:text-lg cursor-pointer text-gray-700 hover:text-black transition-colors"
									>{{ question.question }}</span
								>
							</label>
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
