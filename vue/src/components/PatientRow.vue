<script lang="ts" setup>
import { computed, ref } from 'vue';
import { PatientCell } from '@/pages/PatientsPage.vue';

import AppModal from './AppModal.vue';
import PatientSave from './PatientSave.vue';
import PatientDelete from './PatientDelete.vue';
import { usePatientsStore } from '@/stores';
import { Patient } from '@/assets/data/interfaces';
import SurveyCreate from './SurveyCreate.vue';

interface Props {
	patient: Patient;
	cells: PatientCell[];
}

const props = defineProps<Props>();

const showModal = ref(false);
const successAlert = ref(false);

const patientStore = usePatientsStore();
const labels = patientStore.getLabels;

const keys = Object.keys(labels) as Array<keyof Patient>;

const mappedPatient = computed(() => {
	const mappedPatient: Patient = { ...props.patient };
	const url = import.meta.env.VITE_API_URL;

	if (mappedPatient.email)
		mappedPatient.email = `<a href="mailto:${props.patient.email}" class="font-medium text-blue-600 hover:underline">${props.patient.email}</a>`;

	if (mappedPatient.phone)
		mappedPatient.phone = `<a href="tel:${props.patient.phone}" class="font-medium text-blue-600 hover:underline">${props.patient.phone}</a>`;

	if (mappedPatient.weight) mappedPatient.weight = props.patient.weight + ' kg';
	if (mappedPatient.height) mappedPatient.height = props.patient.height + ' cm';

	if (mappedPatient.consent)
		mappedPatient.consent = `<a href="${url}/public${props.patient.consent}" class="font-medium text-blue-600 hover:underline" target="_blank">Visualizza il file per il consenso</a>`;

	return mappedPatient;
});

const closeModal = () => {
	showModal.value = false;
	successAlert.value = false;
};
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
			<h1 class="text-4xl font-bold my-5">
				{{ patient.fname }} {{ patient.lname }}
			</h1>
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
		</template>
		<template v-slot:button> </template>
	</AppModal>
</template>
