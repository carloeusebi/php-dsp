<script lang="ts" setup>
import { Errors, Survey } from '@/assets/data/interfaces';
import { SurveyCell } from '@/pages/SurveysPage.vue';
import { Ref, computed, ref } from 'vue';
import AppModal from './AppModal.vue';
import SurveyDelete from './SurveyDelete.vue';
import AppButton from './AppButton.vue';
import AppButtonBlank from './AppButtonBlank.vue';
import AppAlert from './AppAlert.vue';
import { useLoaderStore, usePatientsStore } from '@/stores';
import axiosInstance from '@/assets/axios';
import { isAxiosError } from 'axios';

interface Props {
	survey: Survey;
	cells: SurveyCell[];
}

interface Alert {
	show: boolean;
	title?: string;
	message?: string;
	type?: 'warning' | 'success';
}
const emailAlert: Ref<Alert> = ref({ show: false });

const props = defineProps<Props>();
const token = props.survey.token;
const patient = usePatientsStore().getById(props.survey.patient_id);

const showModal = ref(false);

const errors: Ref<Errors> = ref({});
const errorsStr = computed(() => {
	const keys = Object.keys(errors.value);
	return keys.reduce((str, key) => (str += `${errors.value[key]}<br>`), '');
});
const patientEmail = computed(() =>
	patient?.email
		? `<a href="mailto:${patient.email}" class="font-medium text-blue-600 hover:underline">${patient.email}</a>`
		: 'Nessun indirizzo email inserito'
);

const link = `${import.meta.env.VITE_BASE_URL}/admin/test?token=${token}`;

const completedIcon = computed(() => {
	return props.survey.completed ? 'square-check' : 'square';
});

const copyUrl = async () => {
	try {
		await navigator.clipboard.writeText(link);
	} catch (e) {
		alert(e);
	}
};

const showWarningAlert = () => {
	emailAlert.value.show = true;
	emailAlert.value.title = 'Qualcosa è andato storto';
	emailAlert.value.message = errorsStr.value;
	emailAlert.value.type = 'warning';
};

const showSuccessAlert = () => {
	emailAlert.value.show = true;
	emailAlert.value.title = 'Successo';
	emailAlert.value.message = 'Email inviata correttamente';
	emailAlert.value.type = 'success';
};

const sendEmail = async () => {
	emailAlert.value.show = false;

	const loader = useLoaderStore();
	loader.setLoader();

	const data = {
		email: patient?.email,
		subject: 'Questionario per la valutazione',
		body: `<a href="${link}">Link al questionario per la valutazione psicologica</a>`,
	};

	try {
		await axiosInstance.post('/email', data);
		showSuccessAlert();
	} catch (err) {
		if (isAxiosError(err)) {
			console.warn(err.response?.data);
			errors.value = err.response?.data;
			showWarningAlert();
		} else console.error(err);
	} finally {
		loader.unsetLoader();
	}
};

const handleCloseModal = () => {
	showModal.value = false;
	emailAlert.value.show = false;
};
</script>

<template>
	<!-- TABLE ROW -->
	<tr class="text-gray-700 hover:bg-gray-100 transition-colors">
		<td
			v-for="cell in cells"
			:key="cell.key"
			class="px-4 py-3 text-ms border"
			:class="{ 'font-semibold': cell.key === 'patient_name' }"
		>
			<!-- CELL CONTENT -->

			<div
				v-if="cell.key === ('completed' as keyof Survey)"
				class="flex justify-center"
			>
				<font-awesome-icon
					:icon="['far', completedIcon]"
					size="xl"
				/>
			</div>
			<span v-else>{{ survey[cell.key] || '-' }}</span>
		</td>

		<!-- DETAILS BUTTON -->
		<td class="px-4 py-3 text-sm border text-center">
			<span
				@click="showModal = true"
				class="px-3 py-2 font-semibold leading-tight text-sky-700 bg-sky-100 rounded-sm cursor-pointer select-none hover:bg-sky-200 transition-colors"
			>
				Dettagli
			</span>
		</td>
	</tr>

	<AppModal
		:open="showModal"
		@close="handleCloseModal"
		dimensions="container lg:w-8/12 h-full"
	>
		<template #content>
			<div class="flex justify-between items-center">
				<h2 class="mb-0">{{ survey.title }}</h2>
				<SurveyDelete :to-delete-survey="survey" />
			</div>
			<hr class="my-5" />
			<!-- ALERT -->
			<AppAlert
				class="mb-5"
				:show="emailAlert.show"
				:title="emailAlert.title"
				:type="emailAlert.type"
				:message="emailAlert.message"
			/>
			<!-- patient name -->
			<p><strong>Paziente: </strong>{{ survey.patient_name }}</p>
			<p><strong>Email: </strong><span v-html="patientEmail"></span></p>
			<p><strong>Creato il: </strong>{{ survey.created_at }}</p>
			<p><strong>Ultima modifica: </strong>{{ survey.last_update || '-' }}</p>
			<!-- link -->
			<p>
				<strong>Link al sondaggio per il paziente: </strong>
				<AppButtonBlank
					@click="copyUrl"
					color="gray"
				>
					<font-awesome-icon
						:icon="['fas', 'copy']"
						size="lg"
					/>
					Copia
				</AppButtonBlank>
			</p>
		</template>
		<template #button>
			<router-link
				target="_blank"
				:to="{ name: 'results', params: { id: survey.id } }"
			>
				<AppButton>Visualizza Risposte</AppButton>
			</router-link>
			<AppButton
				:disabled="survey.completed || !patient?.email"
				:class="{ 'btn-disabled': survey.completed || !patient?.email }"
				@click="sendEmail"
				class="me-3"
				>Invia un'email<span class="hidden md:block">
					con il link</span
				></AppButton
			>
		</template>
	</AppModal>
</template>
