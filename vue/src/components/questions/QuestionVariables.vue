<script lang="ts" setup>
import { ref } from 'vue';

import AppButton from '../AppButton.vue';
import AppButtonBlank from '../AppButtonBlank.vue';
import AppModal from '../AppModal.vue';
import AppInputElement from '../AppInputElement.vue';

import { QuestionItemI, QuestionVariableI } from '@/assets/data/interfaces';
import { useGenerateId } from '@/composables';
import QuestionVariableItems from './QuestionVariableItems.vue';
import QuestionVariableCutoffs from './QuestionVariableCutoffs.vue';

interface Props {
	modelValue: QuestionVariableI[];
	items: QuestionItemI[];
}

interface Modal {
	show: boolean;
	variable?: QuestionVariableI;
}

const props = defineProps<Props>();
const emit = defineEmits(['update:modelValue', 'add-variable']);

const deleteModal = ref<Modal>({ show: false });
const variableNameInputs = ref<InstanceType<typeof AppInputElement>[] | null>();

const closeDeleteModal = () => {
	deleteModal.value.show = false;
};

// VARIABLES

/**
 * Adds a new variable to the list of variables.
 */
const addNewVariable = () => {
	const id = props.modelValue.length > 0 ? useGenerateId(props.modelValue) : 1;
	props.modelValue.push({ id, name: '', items: [], cutoffs: [] });
	emit('update:modelValue', props.modelValue);
	emit('add-variable');
	setTimeout(() => {
		variableNameInputs.value?.at(-1)?.inputElement?.focus();
	}, 50);
};

/**
 * Deletes a variable from the list of variables.
 * @param toDeleteId The ID of the variable to delete.
 */
const deleteVariable = (toDeleteId: number) => {
	if (toDeleteId) {
		const filteredVars = props.modelValue.filter(({ id }) => id !== toDeleteId);
		emit('update:modelValue', filteredVars);
	}
	closeDeleteModal();
};

/**
 * Updates Items or Cutoffs of a given Variable, when changes are saved. The `prop` argument specifies which one to update
 * @param newArray The updated Array of items or cutoffs.
 * @param variableId The ID of the variable to update.
 * @param prop Specifies what props to update, if `items` or `cutoffs`
 */
const updateVariable = <T>(newArray: T[], variableId: number, prop: 'items' | 'cutoffs') => {
	const indexToUpdate = props.modelValue.findIndex(({ id }) => id === variableId);
	if (indexToUpdate === -1) return;
	(props.modelValue[indexToUpdate][prop] as T[]) = [...newArray];
};
</script>

<template>
	<!-- this add variable button will appear only if the list of variables is longer than 5 -->
	<AppButtonBlank
		v-if="modelValue.length > 5"
		@click="addNewVariable"
		label="Aggiungi una nuova variabile"
	/>
	<ul>
		<li v-for="variable in modelValue">
			<div class="grid grid-cols-3 gap-5 justify-center items-center mb-3">
				<!-- NAME -->

				<div class="flex-grow me-10 col-span-3 md:col-span-2">
					<AppInputElement
						ref="variableNameInputs"
						v-model="variable.name"
						label="Nome della variabile"
						:required="true"
					/>
				</div>

				<div class="col-span-3 md:col-span-1 flex items-center">
					<!-- OPEN ITEMS BUTTON -->
					<QuestionVariableItems
						:items="items"
						:variable-items="variable.items"
						@save="updateVariable($event, variable.id, 'items')"
					/>

					<!-- OPEN CUTOFFS BUTTON -->
					<QuestionVariableCutoffs
						:variable-cutoffs="variable.cutoffs"
						v-model="variable.sexScores"
						@save="updateVariable($event, variable.id, 'cutoffs')"
					/>

					<!-- DELETE BUTTON -->
					<font-awesome-icon
						@click="
							deleteModal.show = true;
							deleteModal.variable = variable;
						"
						class="ms-3 cursor-pointer text-red-700 hover:text-red-800 mb-2 md:mb-0"
						:icon="['fas', 'trash-can']"
					/>
				</div>
			</div>
			<hr class="my-5" />
		</li>
	</ul>
	<AppButtonBlank
		@click="addNewVariable"
		icon="plus"
		label="Aggiungi una nuova variabile"
	/>

	<!-- DELETE MODAL -->
	<AppModal
		:open="deleteModal.show"
		@close="deleteModal.show = false"
		dimensions="max-w-md"
	>
		<template #content>
			<h2>Elimina</h2>
			<hr class="my-2" />
			Sicuro di voler eliminare {{ deleteModal.variable?.name || 'questa variabile' }}?
		</template>
		<template #button>
			<AppButton
				color="red"
				@click="deleteVariable(deleteModal.variable?.id as number)"
			>
				Elimina
			</AppButton>
		</template>
	</AppModal>
</template>
