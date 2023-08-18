<script lang="ts" setup>
import { ref } from 'vue';

import AppModal from '@/components/AppModal.vue';
import AppButton from '@/components/AppButton.vue';
import AppButtonBlank from '@/components/AppButtonBlank.vue';

import { Patient } from '@/assets/data/interfaces';
import { usePatientsStore } from '@/stores';
import { useDeleteFromStore } from '@/composables';

const showDeleteModal = ref(false);

interface Props {
	toDeletePatient: Patient;
}

const props = defineProps<Props>();

const handleDeletePatient = async () => {
	const patientStore = usePatientsStore();
	const id: number = props.toDeletePatient.id || -1;

	useDeleteFromStore(patientStore, id);
};
</script>

<template>
	<AppButtonBlank
		@click="showDeleteModal = true"
		color="red"
		icon="trash-can"
		label="Elimina Paziente"
	/>

	<AppModal
		:open="showDeleteModal"
		@close="showDeleteModal = false"
	>
		<!-- CONTENT -->
		<template v-slot:content>
			<h2 class="text-2xl font-medium">Elimina {{ toDeletePatient.fname }} {{ toDeletePatient.lname }}</h2>
			<hr class="my-3" />
			<p>
				Sei sicuro di voler eliminare <strong>{{ toDeletePatient.fname }} {{ toDeletePatient.lname }}?</strong>
			</p>
			<p>Questa operazione <strong>non è reversibile</strong>.</p>
			<!-- FORM -->
			<form
				@submit.prevent="handleDeletePatient"
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
					>Sono sicuro di voler eliminare
					<strong>{{ toDeletePatient.fname }} {{ toDeletePatient.lname }}</strong></label
				>
			</form>
		</template>
		<!-- BUTTON -->
		<template v-slot:button>
			<AppButton
				form="delete-form"
				color="red"
			>
				Elimina {{ toDeletePatient.fname }} {{ toDeletePatient.lname }}
			</AppButton>
		</template>
	</AppModal>
</template>
