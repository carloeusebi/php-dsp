<script lang="ts" setup>
import { ref } from 'vue';

import AppButton from '../AppButton.vue';
import AppInputElement from '../AppInputElement.vue';

import { Patient } from '@/assets/data/interfaces';
import axiosInstance from '@/assets/axios';

const handelFormSubmit = async () => {
	const params = {
		token: props.token,
		...patientInfos.value,
	};
	try {
		await axiosInstance.post('tests/patient', params);
	} catch (err) {
		console.log(err);
	} finally {
		emit('form-submit');
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

const emit = defineEmits(['form-submit']);

const patientInfos = ref<Partial<Patient>>({
	id: props.patientId,
	weight: '',
	height: '',
	qualification: '',
	job: '',
	cohabitants: '',
});

// fetches the patient
</script>

<template>
	<div class="container md:max-w-4xl mx-auto my-8">
		<h2 class="text-center text-3xl">Inserisci prima delle informazioni su di te</h2>
		<hr class="my-8" />
		<!-- FORM -->
		<form
			@submit.prevent="handelFormSubmit"
			class="mb-8"
		>
			<div class="grid md:grid-cols-2 gap-10">
				<div>
					<AppInputElement
						v-model="patientInfos.weight"
						label="Peso in kg"
						:required="true"
					/>
				</div>
				<div>
					<AppInputElement
						v-model="patientInfos.height"
						label="Altezza in cm"
						:required="true"
					/>
				</div>
				<div>
					<AppInputElement
						v-model="patientInfos.qualification"
						label="Titolo di studio"
						:required="true"
					/>
				</div>
				<div>
					<AppInputElement
						v-model="patientInfos.job"
						label="Professione attuale"
						:required="true"
					/>
				</div>
				<div class="col-span-2">
					<AppInputElement
						v-model="patientInfos.cohabitants"
						label="Vivo con"
						:required="true"
					/>
				</div>
			</div>
			<!-- FORM BUTTON -->
			<hr class="my-5" />
			<div class="flex justify-end">
				<AppButton color="green"> Conferma </AppButton>
			</div>
		</form>
	</div>
</template>

<style scoped></style>
