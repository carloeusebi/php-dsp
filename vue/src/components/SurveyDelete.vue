<script lang="ts" setup>
import { ref } from 'vue';
import AppButton from './AppButton.vue';
import AppModal from './AppModal.vue';
import { useLoaderStore, useSurveysStore } from '@/stores';
import { Survey } from '@/assets/data/interfaces';
import axios from 'axios';
import AppButtonBlank from './AppButtonBlank.vue';

interface Props {
	toDeleteSurvey: Survey;
}

const props = defineProps<Props>();

const showDeleteModal = ref(false);

const handleDeleteSurvey = async () => {
	const surveysStore = useSurveysStore();
	const loader = useLoaderStore();

	const id: number = props.toDeleteSurvey.id || -1;

	try {
		await surveysStore.delete(id);
	} catch (err) {
		if (axios.isAxiosError(err)) console.error(err.response?.data);
		else console.log(err);
	} finally {
		loader.unsetLoader();
	}
};
</script>

<template>
	<AppButtonBlank
		@click="showDeleteModal = true"
		color="red">
		<font-awesome-icon :icon="['fas', 'trash-can']" />
		<span class="hidden md:inline ms-3">Elimina Sondaggio</span>
	</AppButtonBlank>

	<AppModal
		:open="showDeleteModal"
		@close="showDeleteModal = false">
		<!-- CONTENT -->
		<template v-slot:content>
			<h2 class="text-2xl font-medium">Elimina</h2>
			<hr class="my-3" />
			<p>
				Sei sicuro di voler eliminare <strong>{{ toDeleteSurvey.title }}?</strong>
			</p>
			<p>Questa operazione <strong>non è reversibile</strong>.</p>
			<!-- FORM -->
			<form
				@submit.prevent="handleDeleteSurvey"
				id="delete-form"
				class="flex items-center my-3">
				<input
					class="cursor-pointer"
					type="checkbox"
					id="confirm-delete"
					required />
				<label
					for="confirm-delete"
					class="ps-3 cursor-pointer"
					>Sono sicuro di voler eliminare <strong>{{ toDeleteSurvey.title }}</strong></label
				>
			</form>
		</template>
		<!-- BUTTON -->
		<template v-slot:button>
			<AppButton
				form="delete-form"
				color="red">
				Elimina {{ toDeleteSurvey.title }}
			</AppButton>
		</template>
	</AppModal>
</template>

<style scoped></style>
