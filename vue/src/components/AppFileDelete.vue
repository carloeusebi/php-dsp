<script lang="ts" setup>
import axiosInstance from '@/assets/axios';
import { AppFile } from '@/assets/data/interfaces';
import { useLoaderStore, usePatientsStore } from '@/stores';
import { isAxiosError } from 'axios';

interface Props {
	file: AppFile;
}

const props = defineProps<Props>();

const deleteFile = async () => {
	const loader = useLoaderStore();
	loader.setLoader();

	const data = { id: props.file.id, name: props.file.name };

	try {
		await axiosInstance.delete('file', { data });
		if (props.file.patient_id) {
			usePatientsStore().deletePatientFile(props.file.patient_id, props.file.id);
		}
	} catch (err) {
		if (isAxiosError(err)) console.warn(err.response?.data);
		else console.error(err);
	} finally {
		loader.unsetLoader();
	}
};
</script>

<template>
	<div
		class="absolute bg-white px-4 py-2 z-10 border border-gray-200 left-20 top-3 text-sm cursor-pointer shadow-md hover:bg-gray-50 select-none"
		@click="deleteFile"
	>
		Cancella {{ file.name }}
	</div>
</template>

<style scoped></style>
