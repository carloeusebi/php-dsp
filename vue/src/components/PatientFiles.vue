<script lang="ts" setup>
import { Ref, computed, ref } from 'vue';

import AppButton from './AppButton.vue';
import AppModal from './AppModal.vue';

import { Errors, Patient } from '@/assets/data/interfaces';
import AppInputElement from './AppInputElement.vue';
import AppAlert from './AppAlert.vue';

interface Props {
	patient: Patient;
}

const props = defineProps<Props>();

const showModal = ref(false);

const showAlert = computed(() => errorsStr.value.length > 0 || props.patient.consent?.length === 0);

const errors: Ref<Errors> = ref({});
const errorsStr = computed(() => {
	const keys = Object.keys(errors.value);
	return keys.reduce((str, key) => (str += `${errors.value[key]}<br>`), '');
});

const getUrl = (file: string): string => import.meta.env.VITE_API_URL + file;

const loadFile = (event: Event): void => {
	errors.value = {};

	const file = (event.target as HTMLInputElement)?.files?.[0];

	if (!file) return;

	if (file.size > 3_145_728) errors.value['wrong-dimensions'] = 'Dimensioni massime del file 3MB!';
	if (file.type !== 'application/pdf') errors.value['wrong-file-type'] = 'Il file deve essere formato PDF';
};
</script>

<template>
	<AppButton @click="showModal = true">
		<font-awesome-icon :icon="['fas', 'file-pdf']" />
		<span class="ms-3">Visualizza Files del Paziente</span>
	</AppButton>

	<AppModal
		:open="showModal"
		@close="showModal = false"
	>
		<template #content>
			<h2>Files di {{ patient.fname }} {{ patient.lname }}</h2>
			<hr class="my-5" />
			<div class="my-5">
				<!-- ALERT -->
				<AppAlert
					:show="showAlert"
					:title="errorsStr ? 'Attenzione' : ''"
					:message="errorsStr || `${patient.fname} ${patient.lname} non ha nessun file caricato.`"
					:type="errorsStr ? 'warning' : 'info'"
				/>

				<!-- LIST OF FILES -->
				<ul class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6">
					<li
						v-for="(file, i) in patient.consent"
						:key="i"
					>
						<a
							:href="getUrl(file)"
							target="_blank"
						></a>
					</li>
				</ul>
			</div>
			<div>
				<AppInputElement
					label="Carica un altro file"
					type="file"
					@custom-change="loadFile"
				/>
			</div>
		</template>
		<template #button></template>
	</AppModal>
</template>

<style scoped></style>
