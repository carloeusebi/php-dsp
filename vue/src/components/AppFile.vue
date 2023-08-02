<script lang="ts" setup>
import { Ref, ref } from 'vue';
import { onClickOutside } from '@vueuse/core';

import AppFileDelete from '@/components/AppFileDelete.vue';

import axiosInstance from '@/assets/axios';
import { AppFile } from '@/assets/data/interfaces';
import { useLoaderStore } from '@/stores';

interface Props {
	file: AppFile;
}

const props = defineProps<Props>();

const imgSrc = new URL(`../assets/img/${props.file.type}.png`, import.meta.url).href;
const loader = useLoaderStore();

const showContextmenu = ref(false);

const clickOutsideRef: Ref<HTMLElement | null> = ref(null);

onClickOutside(clickOutsideRef, () => (showContextmenu.value = false));

/**
 * Retrieves a file from the server and opens it in a new browser window for viewing.
 * @param {string} fileName - The name of the file to be retrieved and viewed.
 */
const viewFile = async (fileName: string) => {
	if (!fileName) return;

	loader.setLoader();
	try {
		const response = await axiosInstance.get(`file?file_name=${fileName}`, { responseType: 'blob' });

		// Access the 'Content-Type' header
		const type = response.headers['content-type'] as string;

		// Create a Blob from the response data with the appropriate type
		const fileBlob = new Blob([response.data], { type });

		// Create a URL for the file Blob so that it can be opened in a new browser window
		const fileURL = window.URL.createObjectURL(fileBlob);
		window.open(fileURL);
	} catch (err) {
		console.error(err);
	} finally {
		loader.unsetLoader();
	}
};
</script>

<template>
	<div class="relative flex">
		<img
			class="max-h-[20px]"
			:src="imgSrc"
			:alt="file.name"
		/>
		<div
			ref="clickOutsideRef"
			@click="viewFile(file.name)"
			@contextmenu.prevent="showContextmenu = !showContextmenu"
			class="text-sm ms-3 cursor-pointer hover:underline transition"
		>
			{{ file.name.substring(5) }}
		</div>
		<AppFileDelete
			v-if="showContextmenu"
			:file="file"
		/>
	</div>
</template>

<style scoped></style>
