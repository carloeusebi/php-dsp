<script lang="ts" setup>
import { ref } from 'vue';

import AppButton from '@/components/AppButton.vue';
import AppModal from '@/components/AppModal.vue';
import AppButtonBlank from '@/components/AppButtonBlank.vue';

import { useSurveysStore } from '@/stores';
import { Survey } from '@/assets/data/interfaces';
import { useDeleteFromStore } from '@/composables';

interface Props {
	toDeleteSurvey: Survey;
}

const props = defineProps<Props>();

const showDeleteModal = ref(false);

const handleDeleteSurvey = async () => {
	const surveysStore = useSurveysStore();
	const id: number = props.toDeleteSurvey.id || -1;

	useDeleteFromStore(surveysStore, id);
};
</script>

<template>
	<AppButtonBlank
		@click="showDeleteModal = true"
		color="red"
		icon="trash-can"
		label="Elimina Sondaggio"
	/>

	<AppModal
		:open="showDeleteModal"
		@close="showDeleteModal = false"
	>
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
				class="flex items-center my-3"
			>
				<input
					class="cursor-pointer"
					type="checkbox"
					id="confirm-delete"
					required
				/>
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
				color="red"
			>
				Elimina {{ toDeleteSurvey.title }}
			</AppButton>
		</template>
	</AppModal>
</template>

<style scoped></style>
