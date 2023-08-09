<script lang="ts" setup>
import { Ref, computed, ref } from 'vue';

import AppModal from '@/components/AppModal.vue';
import AppTable from '@/components/AppTable.vue';
import SurveyRow from '@/components/surveys/SurveyRow.vue';
import SurveyCreate from '@/components/surveys/SurveyCreate.vue';
import PatientDelete from './PatientDelete.vue';
import PatientSave from './PatientSave.vue';
import PatientFiles from './PatientFiles.vue';

import { PatientCell } from '@/pages/PatientsPage.vue';
import { usePatientsStore, useSurveysStore } from '@/stores';
import { Patient, Survey } from '@/assets/data/interfaces';
import { OrderSurvey, SurveyCell } from '@/pages/SurveysPage.vue';
import { useSort } from '@/composables';

interface Props {
	patient: Patient;
	cells?: PatientCell[];
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

/**************************************************************************** */
/***************************       SURVEYS       **************************** */
/**************************************************************************** */

const getPatientsSurveys = (id: number): Survey[] | null => {
	const allSurveys = useSurveysStore().getSurveys;
	return allSurveys.filter(({ patient_id }) => patient_id === id);
};

const sortSurveys = (newOrder: OrderSurvey) => {
	orderSurvey.value = { ...newOrder };
};

const patientSurveys = computed(() => getPatientsSurveys(props.patient.id as number));
const orderSurvey = ref<OrderSurvey>({ by: 'last_update', type: 'up' });
const orderedSurveys = computed(() =>
	useSort(patientSurveys.value as Survey[], orderSurvey.value.by, orderSurvey.value.type)
);

const surveyCell: Ref<SurveyCell[]> = ref([
	{ label: 'Titolo', key: 'title' },
	{ label: 'Ultima modifica', key: 'last_update' },
	{ label: '', key: 'completed' },
]);
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
			<div>
				<PatientFiles :patient="patient" />
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
			<div class="grid lg:grid-cols-2 gap-6">
				<div class="col-span-1">
					<ul>
						<!-- PATIENT DETAILS -->
						<li
							v-for="key in keys"
							:key="key"
						>
							<!-- to not print id  -->
							<div v-if="key !== 'id'">
								<strong>{{ labels[key] }}: </strong>
								<span v-html="mappedPatient[key]"></span>
							</div>
						</li>
					</ul>
				</div>
				<hr class="lg:hidden my-1" />
				<!-- SURVEYS TABLE -->
				<div class="survey-table col-span-1 overflow-auto">
					<AppTable
						v-if="(patientSurveys as Survey[]).length > 0"
						:cells="surveyCell"
						@sort-change="sortSurveys"
					>
						<template #tbody>
							<SurveyRow
								v-for="survey in orderedSurveys"
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
		<template v-slot:button> </template>
	</AppModal>
</template>

<style scoped lang="scss">
.survey-table {
	// completed row
	:deep(th:nth-of-type(3)),
	:deep(td:nth-of-type(3)) {
		width: 30px;
	}

	// details row
	:deep(th:last-of-type),
	:deep(td:last-of-type) {
		width: 75px;
	}

	// last_update row
	:deep(th:nth-child(2)),
	:deep(td:nth-child(2)) {
		display: none;
	}

	@media screen and (min-width: 650px) {
		:deep(th:nth-child(2)),
		:deep(td:nth-child(2)) {
			display: table-cell;
		}
	}
}
</style>
