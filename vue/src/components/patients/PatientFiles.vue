<script lang="ts" setup>
import { Ref, computed, ref } from 'vue';
import axiosInstance from '@/assets/axios';
import { isAxiosError } from 'axios';

import AppButton from '@/components/AppButton.vue';
import AppModal from '@/components/AppModal.vue';
import AppAlert from '@/components/AppAlert.vue';
import AppInputElement from '@/components/AppInputElement.vue';
import AppButtonBlank from '@/components/AppButtonBlank.vue';

import { Errors, Patient } from '@/assets/data/interfaces';
import { useLoaderStore } from '@/stores';
import AppFile from '../AppFile.vue';

interface Props {
	patient: Patient;
}

const loader = useLoaderStore();

const props = defineProps<Props>();

const toUploadFile: Ref<File | null> = ref(null);
const showModal = ref(false);

const fileInputElement = ref<InstanceType<typeof AppInputElement> | null>(null);

const showAlert = computed(() => errorsStr.value.length > 0 || props.patient.files?.length === 0);

const errors: Ref<Errors> = ref({});
const errorsStr = computed(() => {
	const keys = Object.keys(errors.value);
	return keys.reduce((str, key) => (str += `${errors.value[key]}<br>`), '');
});

const handleCloseModal = () => {
	showModal.value = false;
	setTimeout(() => {
		errors.value = {};
	}, 500);
};

const unloadFile = () => {
	(fileInputElement.value?.inputElement as HTMLInputElement).value = '';
};

/**
 * Handles the file loading event and performs validation on the selected file.
 *
 * This function is triggered when a file is selected through an input element with type="file".
 * @param {Event} event The event object triggered when a file is selected.
 */
const loadFile = (event: Event): void => {
	errors.value = {};

	const loadedFile = (event.target as HTMLInputElement)?.files?.[0];

	if (!loadedFile) return;

	if (loadedFile.size > 3_145_728) errors.value['wrong-dimensions'] = 'Dimensioni massime del file 3MB!';
	if (loadedFile.type !== 'application/pdf') errors.value['wrong-file-type'] = 'Il file deve essere formato PDF';

	if (!errorsStr.value) {
		// load the file in the ref
		toUploadFile.value = loadedFile;
	} else {
		// empty the file file input
		unloadFile();
	}
};

/**
 * Uploads a file to the server using Axios and FormData.
 */
const uploadFile = async () => {
	errors.value = {};

	if (!toUploadFile.value) return;

	loader.setLoader();

	const formData = new FormData();
	formData.append('patient_id', (props.patient.id as number).toString());
	formData.append('file', toUploadFile.value);

	const headers = { headers: { 'Content-Type': 'multipart/form-data' } };

	try {
		const response = await axiosInstance.post('file', formData, headers);

		// Update the patient's files list with the newly uploaded file
		const justUploadedFile = response.data.last_insert;
		props.patient.files?.push(justUploadedFile);
		unloadFile();
	} catch (err) {
		if (isAxiosError(err)) errors.value = err.response?.data;
		else console.error(err);
	} finally {
		loader.unsetLoader();
	}
};
</script>

<template>
	<AppButtonBlank
		@click="showModal = true"
		color="yellow"
		icon="file-pdf"
		label="Visualizza filed del paziente"
	/>

	<AppModal
		:open="showModal"
		@close="handleCloseModal"
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
				<div class="my-5">
					<ul>
						<li
							v-for="(file, i) in patient.files"
							:key="i"
							class="my-3"
						>
							<AppFile :file="file" />
						</li>
					</ul>
				</div>
			</div>
			<hr class="my-5" />
			<div>
				<form
					id="file-form"
					@submit.prevent="uploadFile"
				>
					<AppInputElement
						label="Carica un altro file"
						type="file"
						ref="fileInputElement"
						@custom-change="loadFile"
					/>
				</form>
			</div>
		</template>
		<template #button>
			<AppButton
				form="file-form"
				:disabled="!toUploadFile"
				:class="{ 'btn-disabled': !toUploadFile }"
			>
				<font-awesome-icon :icon="['fas', 'upload']" />
				<span class="ms-3">Carica File</span>
			</AppButton>
		</template>
	</AppModal>
</template>

<style lang="scss" scoped></style>
