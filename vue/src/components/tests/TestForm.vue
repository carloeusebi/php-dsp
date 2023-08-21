<script lang="ts" setup>
import { Ref, computed, ref } from 'vue';

import AppButton from '../AppButton.vue';
import AppAlert from '../AppAlert.vue';
import PatientForm from '../patients/PatientForm.vue';

import { Errors, Patient } from '@/assets/data/interfaces';
import axiosInstance from '@/assets/axios';
import { useLoaderStore } from '@/stores';
import { isAxiosError } from 'axios';
import { useScrollTo } from '@/composables';

const endpoint = 'tests/patient';

const fetchPatient = async (token: string) => {
	loader.setLoader();
	errors.value = {};
	const params = {
		token,
		id: props.patientId,
	};
	try {
		const response = await axiosInstance.get(endpoint, { params });
		patient.value = response.data.patient;
	} catch (err) {
		console.log(err);
		// ignore the error and go on
		emit('form-submit');
	} finally {
		loader.unsetLoader();
	}
};

const handleFormSubmit = async () => {
	//@ts-ignore
	useScrollTo(window, 0);
	loader.setLoader();
	errors.value = {};
	if (patient.value === null) return;
	const params = {
		token: props.token,
		...patient.value,
	};
	try {
		await axiosInstance.post(endpoint, params);
		emit('form-submit');
	} catch (err) {
		if (isAxiosError(err)) {
			// Axios error means the patient information are not correct
			errors.value = err.response?.data;
			console.warn(err);
		} else {
			// If not axios error ignore the error and go on
			console.error(err);
			emit('form-submit');
		}
	} finally {
		loader.unsetLoader();
	}
};

const props = defineProps({
	patientId: {
		type: Number,
		required: true,
	},
	token: {
		type: String,
		required: true,
	},
});

const loader = useLoaderStore();
const emit = defineEmits(['form-submit']);
const patient = ref<Patient | null>(null);

const errors: Ref<Errors> = ref({});
const errorsStr = computed(() => {
	const keys = Object.keys(errors.value);
	return keys.reduce((str, key) => (str += `${errors.value[key]}<br>`), '');
});

fetchPatient(props.token);

// fetches the patient
</script>

<template>
	<div class="container md:max-w-4xl mx-auto py-5">
		<h2 class="text-center text-3xl">Inserisci prima delle informazioni su di te</h2>
		<AppAlert
			:show="errorsStr.length > 0"
			:message="errorsStr"
			type="warning"
			title="Attenzione"
			class="my-4"
		/>
		<hr class="my-8" />

		<!-- FORM -->
		<form @submit.prevent="handleFormSubmit">
			<PatientForm
				v-if="patient"
				@form-emptied="errors = {}"
				:patient="patient"
				:is-test="true"
			/>
			<!-- FORM BUTTON -->
			<hr class="my-5" />
			<div class="flex justify-end items-center">
				<button
					v-if="errorsStr.length > 0"
					type="button"
					class="inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto me-4"
					@click="$emit('form-submit')"
				>
					Salta
				</button>
				<AppButton color="green"> Conferma </AppButton>
			</div>
		</form>
	</div>
</template>

<style scoped></style>
