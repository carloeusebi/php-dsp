<script lang="ts" setup>
import { computed, ref, Ref } from 'vue';

import AppModal from '@/components/AppModal.vue';
import AppAlert from '@/components/AppAlert.vue';
import AppButtonBlank from '@/components/AppButtonBlank.vue';
import AppButton from '@/components/AppButton.vue';
import PatientForm from './PatientForm.vue';

import { Errors, Patient } from '@/assets/data/interfaces';
import { emptyPatient } from '@/assets/data/data';
import { useSaveToStore, useScrollTo } from '@/composables';
import { usePatientsStore } from '@/stores';

interface Props {
	title: string;
	toEditPatient?: Patient;
	icon: string;
	buttonLabel: string;
}

// if no toEditPatient, patient default to an empty one
const props = withDefaults(defineProps<Props>(), {
	toEditPatient: () => ({ ...(emptyPatient as Patient) }),
});

const showModal = ref(false);
const patientRef: Ref<Patient> = ref({ ...props.toEditPatient });
// modal component ref
const modalComponent = ref<InstanceType<typeof AppModal> | null>(null);

const errors: Ref<Errors> = ref({});
const errorsStr = computed(() => {
	const keys = Object.keys(errors.value);
	return keys.reduce((str, key) => (str += `${errors.value[key]}<br>`), '');
});

/**
 * Prepares patient's info and then loads store
 */
const handleSavePatient = async () => {
	errors.value = {};

	const patientStore = usePatientsStore();

	const keys = Object.keys(patientRef.value) as Array<keyof Patient>;
	const patientFormData = new FormData();

	// maps the FormData to the patient's keys, needed for file upload
	keys.forEach(key => {
		if (key === 'id' && !patientRef.value.id) return;
		if (patientRef.value[key]) patientFormData.append(key, patientRef.value[key] as string);
	});

	// the store handles the patient saving

	errors.value = await useSaveToStore(patientFormData, patientStore);

	if (!errorsStr.value) showModal.value = false;
	else useScrollTo(modalComponent.value?.$refs.modal as HTMLTemplateElement, 0); // scrolls the modal to the top, needed to show errors when on smartphones
};
</script>

<template>
	<AppButtonBlank
		@click="showModal = true"
		:icon="icon"
		:label="title"
	/>

	<AppModal
		@close="showModal = false"
		:open="showModal"
		class="relative"
		ref="modalComponent"
	>
		<template v-slot:content>
			<h2>{{ title }}</h2>

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
			<hr class="mb-10" />

			<!-- FORM -->
			<form
				id="patient-form"
				@submit.prevent="handleSavePatient"
			>
				<PatientForm
					@form-emptied="errors = {}"
					:patient="patientRef"
				/>
			</form>
		</template>
		<template v-slot:button>
			<AppButton form="patient-form">
				{{ buttonLabel }}
			</AppButton>
		</template>
	</AppModal>
</template>
