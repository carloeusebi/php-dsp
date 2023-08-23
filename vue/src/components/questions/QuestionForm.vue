<script lang="ts" setup>
import draggable from 'vuedraggable';
import { Ref, computed, reactive, ref } from 'vue';

import AppInputElement from '@/components/AppInputElement.vue';
import QuestionVariables from './QuestionVariables.vue';
import QuestionItem from './QuestionItem.vue';
import QuestionItemMUL from './QuestionItemMUL.vue';

import { useGenerateId } from '@/composables';
import { questionTypes } from '@/assets/data/data';
import { Question, QuestionLegend } from '@/assets/data/interfaces';
import { useQuestionsStore } from '@/stores';
import AppButtonBlank from '../AppButtonBlank.vue';

interface Props {
	question: Question;
}

const labels = useQuestionsStore().getLabels;

const props = defineProps<Props>();
const emit = defineEmits(['answer-added', 'variable-added']);

const form = reactive(props.question);
const types = ref(questionTypes);
const newItem: Ref = ref('');
const newItemReversed = ref(false);

const itemsListRef = ref<HTMLDivElement | null>(null);

// calculate number of answers
const numberOfLegends = computed(() => {
	if (form.type === 'MUL') return;

	const getNumOfLegends = (type: Question['type']) => {
		const low = parseInt(type.at(0) as string);
		const high = parseInt(type.at(-1) as string);
		return high - low + 1;
	};

	const numOfLegends = form.type === 'EDI' ? 6 : getNumOfLegends(form.type);

	// this cycle makes the legend array the same length of the legend inputs in the form, if the array were it shorter it would cause an error when v-modeling the input with an undefined
	while (form.legend.length < numOfLegends) {
		// eslint-disable-next-line vue/no-side-effects-in-computed-properties
		(form.legend as QuestionLegend[]).push({
			id: form.legend.length + 1,
			legend: '',
		});
	}

	return numOfLegends;
});

const getLegendLabel = (i: number): string => {
	if (form.type === 'EDI') {
		return (i <= 2 ? 0 : i - 2).toString();
	}
	return (parseInt(form.type.at(0) as string) + i).toString();
};

const deleteItem = (id: number): void => {
	form.items = form.items.filter(a => a.id !== id);
};

/**
 * Adds a new item to the list of the Questionnaire's items
 */
const addItem = () => {
	const capitalizeItem = (textToCapitalize: string): string =>
		textToCapitalize.at(0)?.toUpperCase() + textToCapitalize.slice(1);

	if (!newItem.value) return;

	const id = useGenerateId(form.items);
	const text = capitalizeItem(newItem.value);
	form.items.push({ id, text, reversed: newItemReversed.value });

	emit('answer-added', itemsListRef.value?.scrollHeight);

	newItem.value = '';
	newItemReversed.value = false;
};

/**
 * Adds a new MUL item to the list of the Questionnaire's items
 */
const addMULItem = () => {
	const id = useGenerateId(form.items);
	const newMULItem = {
		id,
		text: '',
		hasMultipleAnswers: true,
		multipleAnswers: [],
	};

	form.items.push(newMULItem);
	emit('answer-added', itemsListRef.value?.scrollHeight);
};
</script>

<template>
	<div class="grid md:grid-cols-3 md:gap-6 mb-6">
		<!-- TITLE -->
		<div class="md:col-span-2 mb-4">
			<AppInputElement
				v-model="form.question"
				:label="labels.question"
			/>
		</div>

		<!-- TYPE -->
		<div class="md:col-span-1">
			<AppInputElement
				v-model="form.type"
				:label="labels.type"
				type="select"
			>
				<option
					v-for="t in types"
					:key="t"
					:value="t"
				>
					{{ t }}
				</option>
			</AppInputElement>
		</div>
	</div>

	<!-- DESCRIPTION TEXTAREA -->
	<div class="relative mb-6">
		<AppInputElement
			v-model="form.description"
			:label="labels.description"
			type="textarea"
		/>
	</div>
	<hr class="my-5" />
	<!-- ! IF MULTI ANSWERS MUL -->
	<div v-if="form.type === 'MUL'">
		<div ref="itemsListRef">
			<draggable
				item-key="id"
				tag="ul"
				v-model="form.items"
				:animation="450"
				:delay="750"
			>
				<template #item="{ element: item, index }">
					<li>
						<QuestionItemMUL
							:index="index + 1"
							:item="item"
							@delete-item="deleteItem(item.id)"
						/>
					</li>
				</template>
			</draggable>
		</div>
		<AppButtonBlank
			@click="addMULItem"
			icon="plus"
			label="Aggiungi domanda"
		/>
	</div>
	<!-- * ELSE -->
	<div v-else>
		<p class="text-gray-500 mb-3">
			{{ labels.legend }}
		</p>

		<!-- LEGEND -->
		<div class="grid md:grid-cols-2 md:gap-x-6 relative mb-8">
			<!-- @vue-ignore -->
			<div
				v-for="(n, i) in numberOfLegends"
				:key="i"
				class="mb-3"
			>
				<AppInputElement
					:label="getLegendLabel(i)"
					v-model="form.legend[i].legend"
				/>
			</div>
		</div>
		<hr class="my-5" />
		<p class="text-gray-500 mb-3">{{ labels.items }} - Spuntare quelle a punteggio invertito</p>

		<div ref="itemsListRef">
			<!-- ITEMS -->
			<draggable
				item-key="id"
				tag="ul"
				v-model="form.items"
				:animation="150"
				:delay="750"
			>
				<template #item="{ element: item, index }">
					<li>
						<QuestionItem
							:item="item"
							:index="index"
							@delete-item="deleteItem(item.id)"
						/>
					</li>
				</template>
			</draggable>
		</div>

		<!-- ADD NEW ITEM -->
		<div class="flex items-end bg-white">
			<div class="grow">
				<label class="container shrink">
					<input
						v-model="newItemReversed"
						type="checkbox"
						class="me-2 cursor-pointer"
					/>
					<span class="checkmark"></span>
				</label>
				<AppInputElement
					@keydown.enter.prevent="addItem"
					@keyup.ctrl="addItem"
					@keyup.ctrl.v="addItem"
					class="grow ms-8"
					v-model.trim="newItem"
					id="new-answer"
				/>
			</div>
			<font-awesome-icon
				@click="addItem"
				class="ms-3 cursor-pointer text-blue-700 hover:text-blue-800"
				:icon="['fas', 'plus']"
			/>
		</div>
	</div>

	<hr class="my-8" />
	<p class="text-gray-500 mb-8">Variabili</p>

	<!-- VARIABLES -->
	<QuestionVariables
		v-model="question.variables"
		:items="question.items"
		@add-variable="emit('variable-added', -1)"
	/>
</template>

<style lang="scss" scoped>
@use '@/assets/scss/checkbox';
</style>
