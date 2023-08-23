<script lang="ts" setup>
import { ref } from 'vue';

import { QuestionItemI } from '@/assets/data/interfaces';
import AppInputElement from '../AppInputElement.vue';

import { useGenerateId } from '@/composables';

interface Props {
	index: number;
	item: QuestionItemI;
}

const props = defineProps<Props>();
const emit = defineEmits(['delete-item']);

const newAnswer = ref('');

/**
 * Adds a new Answer.
 */
const addAnswer = () => {
	const capitalizeItem = (textToCapitalize: string): string =>
		textToCapitalize.at(0)?.toUpperCase() + textToCapitalize.slice(1);

	if (!newAnswer.value) return;

	if (!props.item.multipleAnswers) props.item.multipleAnswers = [];

	const id = useGenerateId(props.item.multipleAnswers);
	const customAnswer = capitalizeItem(newAnswer.value);
	const points = props.item.multipleAnswers.length;
	props.item.multipleAnswers?.push({ id, customAnswer, points });

	newAnswer.value = '';
};

/**
 * Deletes an answer.
 * @param id The ID of the answer to delete.
 */
const deleteAnswer = (toDeleteId: number) => {
	props.item.multipleAnswers = props.item.multipleAnswers?.filter(({ id }) => id !== toDeleteId);
};
</script>

<template>
	<div class="my-3 p-3 bg-gray-50 shadow-inner rounded-lg">
		<div class="flex justify-between">
			<div>
				<!-- COUNTER -->
				<div>{{ index }}.</div>

				<!-- REVERSE CHECKBOX -->
				<div>
					<!-- CHECKBOX -->

					<label class="container">
						<input
							:id="`reversed-item-id-${item.id}`"
							v-model="item.reversed"
							type="checkbox"
							class="me-2 cursor-pointer"
						/>
						<span class="checkmark"></span>
					</label>
					<label
						:for="`reversed-item-id-${item.id}`"
						class="ms-8 cursor-pointer select-none"
					>
						Domanda a punteggio invertito
					</label>
				</div>
			</div>

			<!-- DELETE ITEM BUTTON -->
			<font-awesome-icon
				:icon="['fas', 'trash-can']"
				size="lg"
				class="text-red-700 hover:text-red-700 cursor-pointer"
				@click="emit('delete-item')"
			/>
		</div>
		<hr class="my-5" />
		<AppInputElement
			v-model="item.text"
			label="Domanda (può essere lasciato vuoto)"
		/>
		<hr class="my-5" />
		<ul class="px-7">
			<li
				v-for="ans in item.multipleAnswers"
				class="flex items-end gap-5 my-2"
			>
				<!-- ANSWER POINTS -->
				<AppInputElement
					v-model="ans.points"
					type="number"
					label="punteggio"
					class="w-[50px]"
					:required="true"
				/>

				<!-- ANSWER TEXT -->
				<AppInputElement
					class="grow"
					v-model="ans.customAnswer"
					:required="true"
				/>

				<!-- DELETE ANSWER BUTTON -->
				<font-awesome-icon
					:icon="['fas', 'trash-can']"
					size="sm"
					class="text-red-700 hover:text-red-700 cursor-pointer p-2"
					@click="deleteAnswer(ans.id)"
				/>
			</li>
		</ul>
		<hr class="my-5" />

		<!-- ADD NEW ANSWER -->
		<div class="flex items-end gap-5">
			<AppInputElement
				class="grow"
				label="Aggiungi un'altra risposta"
				v-model="newAnswer"
				@keydown.enter.prevent="addAnswer"
				@keyup.ctrl="addAnswer"
				@keyup.ctrl.v="addAnswer"
			/>
			<font-awesome-icon
				@click="addAnswer"
				class="me-3 cursor-pointer text-blue-700 hover:text-blue-800"
				:icon="['fas', 'plus']"
			/>
		</div>
	</div>
</template>

<style lang="scss" scoped>
@use '@/assets/scss/checkbox';

label.container {
	bottom: 10px;
}
</style>
