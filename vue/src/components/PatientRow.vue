<script lang="ts" setup>
import { Ref, computed, ref } from 'vue';

import AppModal from './AppModal.vue';
import PatientSave from './PatientSave.vue';
import PatientDelete from './PatientDelete.vue';
import SurveyCreate from './SurveyCreate.vue';
import SurveyRow from './SurveyRow.vue';
import AppTable from './AppTable.vue';
import PatientFiles from './PatientFiles.vue';

import { PatientCell } from '@/pages/PatientsPage.vue';
import { usePatientsStore, useSurveysStore } from '@/stores';
import { Patient, Survey } from '@/assets/data/interfaces';
import { SurveyCell } from '@/pages/SurveysPage.vue';
import { useSort } from '@/composables';

interface Props {
	patient: Patient;
	cells: PatientCell[];
}

const patientStore = usePatientsStore();

const labels = patientStore.getLabels;

const props = defineProps<Props>();

const showModal = ref(false);
const successAlert = ref(false);

const closeModal = () => {
	showModal.value = false;
	successAlert.value = false;
};

const keys = Object.keys(labels) as Array<keyof Patient>;

/**
 * Maps the patient adding formatting to some fields
 */
const mappedPatient = computed(() => {
	const mappedPatient: Patient = { ...props.patient };

	if (mappedPatient.email)
		mappedPatient.email = `<a href="mailto:${props.patient.email}" class="font-medium text-blue-600 hover:underline">${props.patient.email}</a>`;

	if (mappedPatient.phone)
		mappedPatient.phone = `<a href="https://wa.me/${props.patient.phone}" target="_blank" class="font-medium text-blue-600 hover:underline">${props.patient.phone}</a>`;

	if (mappedPatient.weight) mappedPatient.weight = props.patient.weight + ' kg';
	if (mappedPatient.height) mappedPatient.height = props.patient.height + ' cm';

	return mappedPatient;
});

/**
 * Patient's Surveys
 */
const getPatientsSurveys = (id: number): Survey[] | null => {
	const allSurveys = useSurveysStore().getSurveys;
	const patientsSurveys = allSurveys.filter(({ patient_id }) => patient_id === id);
	return useSort(patientsSurveys, 'last_update', 'down');
};
const patientSurveys = computed(() => getPatientsSurveys(props.patient.id as number));
const surveyCell: Ref<SurveyCell[]> = ref([{ label: 'Titolo', key: 'title' }]);
</script>

<template>
	<!-- TABLE ROW -->
	<tr class="text-gray-700 hover:bg-gray-100 transition-colors">
		<td
			v-for="cell in cells"
			:key="cell.key"
			class="px-4 py-3 text-ms border"
			:class="{ 'font-semibold': cell.key === 'fname' || cell.key === 'lname' }"
		>
			{{ patient[cell.key] }}
		</td>
		<td class="px-4 py-3 text-sm border text-center">
			<span
				@click="showModal = true"
				class="px-3 py-2 font-semibold leading-tight text-sky-700 bg-sky-100 rounded-sm cursor-pointer select-none hover:bg-sky-200 transition-colors"
			>
				Dettagli
			</span>
		</td>
	</tr>

	<!-- PATIENT PAGE -->
	<AppModal
		:open="showModal"
		:patient="patient"
		dimensions="container lg:w-full h-full"
		@close="closeModal"
	>
		<template v-slot:content>
			<!-- TOP BUTTONS -->
			<div class="">
				<SurveyCreate :patient="patient" />
				<PatientSave
					title="Modifica il paziente"
					icon="pen"
					button-label="Modifica"
					:to-edit-patient="{ ...patient }"
				/>
				<PatientDelete :to-delete-patient="{ ...patient }" />
			</div>
			<hr />
			<h1 class="text-4xl font-bold my-5">{{ patient.fname }} {{ patient.lname }}</h1>
			<div class="grid md:grid-cols-2 gap-6">
				<div class="col-span-1">
					<ul>
						<!-- PATIENT DETAILS -->
						<li
							v-for="key in keys"
							:key="key"
						>
							<!-- to not print id  -->
							<div v-if="key !== 'id' && key !== 'consent'">
								<strong>{{ labels[key] }}: </strong>
								<span v-html="mappedPatient[key]"></span>
							</div>
						</li>
					</ul>
				</div>
				<!-- SURVEYS TABLE -->
				<div class="col-span-1">
					<AppTable
						v-if="(patientSurveys as Survey[]).length > 0"
						:cells="surveyCell"
						:can-sort="false"
					>
						<template #tbody>
							<SurveyRow
								v-for="survey in patientSurveys"
								:key="survey.id"
								:survey="survey"
								:cells="surveyCell"
							/>
						</template>
					</AppTable>
				</div>
			</div>
		</template>
		<!-- BUTTONS -->
		<template v-slot:button>
			<PatientFiles :patient="patient" />
		</template>
	</AppModal>
</template>
