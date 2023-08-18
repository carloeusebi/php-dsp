<script lang="ts" setup>
import { ref } from 'vue';
import { Tag } from '@/assets/data/interfaces';
import QuestionTagSave from './QuestionTagSave.vue';
import AppModal from '../AppModal.vue';
import AppButton from '../AppButton.vue';

interface Props {
	tag: Tag;
	editable: boolean;
}

const showEditModal = ref(false);
const showDeleteModal = ref(false);

defineProps<Props>();
const emit = defineEmits(['delete']);

const deleteTag = (id: number) => {
	emit('delete', id);
	showDeleteModal.value = false;
};
</script>

<template>
	<div class="flex gap-3">
		<label
			:for="`tag-${tag.id}`"
			class="grow cursor-pointer ms-7 overflow-hidden"
			:style="`color: ${tag.color}`"
		>
			{{ tag.tag }}
		</label>
		<!-- EDIT BUTTON -->
		<div
			v-if="editable"
			class="flex"
		>
			<font-awesome-icon
				role="button"
				@click="showEditModal = true"
				class="text-blue-600 hover:text-blue-800 focus:text-blue-800 p-1"
				:icon="['fas', 'pen']"
			/>
			<!-- DELETE BUTTON -->
			<font-awesome-icon
				role="button"
				@click="showDeleteModal = true"
				class="text-red-600 hover:text-red-800 focus:text-red-800 p-1"
				:icon="['fas', 'trash-can']"
			/>
		</div>
	</div>

	<QuestionTagSave
		:open="showEditModal"
		@close="showEditModal = false"
		type="update"
		:tag="tag"
	/>

	<AppModal
		:open="showDeleteModal"
		@close="showDeleteModal = false"
		dimensions="max-w-sm"
	>
		<template #content>
			<h2>Cancella {{ tag.tag }}</h2>
			<hr class="my-3" />
			<p>Sei sicuro di voler eliminare {{ tag.tag }}</p>
		</template>
		<template #button>
			<AppButton
				color="red"
				@click="deleteTag(tag.id)"
			>
				Elimina
			</AppButton>
		</template>
	</AppModal>
</template>
