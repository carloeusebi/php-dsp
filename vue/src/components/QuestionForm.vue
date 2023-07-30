<script lang="ts" setup>
import { questionTypes } from '@/assets/data/data';
import { Question, QuestionItem, QuestionLegend } from '@/assets/data/interfaces';
import { Ref, computed, reactive, ref } from 'vue';
import AppInputElement from './AppInputElement.vue';
import draggable from 'vuedraggable';
import { useQuestionsStore } from '@/stores';

interface Props {
	question: Question;
}

const labels = useQuestionsStore().getLabels;

const props = defineProps<Props>();

const form = reactive(props.question);
const types = ref(questionTypes);
const newItem: Ref = ref('');
const newItemReversed = ref(false);

// calculate number of answers
const numberOfAnswers = computed(() => {
	const low = parseInt(form.type.at(0) as string);
	const high = parseInt(form.type.at(-1) as string);
	const numOfAnswers = high - low + 1;

	// this cycle makes the legend array the same length of the legend inputs in the form, if the array were it shorter it would cause an error when v-modeling the input with an undefined
	while (form.legend.length < numOfAnswers) {
		// eslint-disable-next-line vue/no-side-effects-in-computed-properties
		(form.legend as QuestionLegend[]).push({
			id: form.legend.length + 1,
			legend: '',
		});
	}

	return numOfAnswers;
});

const getLegendLabel = (i: number): string => (parseInt(form.type.at(0) as string) + i).toString();

const deleteItem = (id: number): void => {
	form.items = form.items.filter(a => a.id !== id);
};

/**
 * Adds a new item to the list of the Questionnaire's items
 */
const addItem = () => {
	/**
	 * Generates a new id for the item
	 * @param items The list of items
	 * @returns The id for the new item
	 */
	const generateId = (items: QuestionItem[]): number => items.reduce((newId, { id }) => (newId > id ? newId : id), 0) + 1;
	const capitalizeItem = (textToCapitalize: string): string => textToCapitalize.at(0)?.toUpperCase() + textToCapitalize.slice(1);

	if (!newItem.value) return;

	const id = generateId(form.items);
	const text = capitalizeItem(newItem.value);
	form.items.push({ id, text, reversed: newItemReversed.value });

	emit('answer-added');

	newItem.value = '';
	newItemReversed.value = false;
};

const emit = defineEmits(['answer-added']);
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
	<p class="text-sm text-gray-500 mb-3">{{ labels.legend }}</p>

	<!-- LEGEND -->
	<div class="grid md:grid-cols-2 md:gap-x-6 relative mb-8">
		<!-- @vue-ignore -->
		<div
			v-for="(n, i) in numberOfAnswers"
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
	<p class="text-sm text-gray-500 mb-3">{{ labels.items }} - Spuntare quelle a punteggio invertito</p>

	<!-- ANSWERS -->
	<!-- @vue-ignore -->
	<draggable
		item-key="id"
		tag="ul"
		v-model="form.items"
		:animation="150"
		:delay="250"
		:delay-on-touch-only="true"
	>
		<template #item="{ element: item }">
			<li class="flex items-end">
				<div class="grow">
					<!-- CHECKBOX -->
					<label class="container shrink">
						<input
							v-model="item.reversed"
							type="checkbox"
							class="me-2 cursor-pointer"
						/>
						<span class="checkmark"></span>
					</label>
					<AppInputElement
						class="grow ms-8"
						v-model="item.text"
						:id="`answer-${item.id}`"
					/>
				</div>
				<font-awesome-icon
					@click="deleteItem(item.id)"
					class="ms-3 cursor-pointer text-red-700 hover:text-red-800 mb-2 md:mb-0"
					:icon="['fas', 'trash-can']"
				/>
			</li>
		</template>
	</draggable>

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
</template>

<style scoped>
/* The container */
label.container {
	display: block;
	position: relative;
	cursor: pointer;
	font-size: 22px;
}

/* Hide the browser's default checkbox */
label.container input {
	position: absolute;
	opacity: 0;
	cursor: pointer;
	height: 0;
	width: 0;
}

/* Create a custom checkbox */
.checkmark {
	position: absolute;
	top: 12px;
	left: 0;
	height: 20px;
	width: 20px;
	background-color: #eee;
}

/* On mouse-over, add a grey background color */
.container:hover input ~ .checkmark {
	background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container input:checked ~ .checkmark {
	background-color: #2196f3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
	content: '';
	position: absolute;
	display: none;
}

/* Show the checkmark when checked */
.container input:checked ~ .checkmark:after {
	display: block;
}

/* Style the checkmark/indicator */
.container .checkmark:after {
	left: 9px;
	top: 5px;
	width: 5px;
	height: 10px;
	border: solid white;
	border-width: 0 3px 3px 0;
	transform: rotate(45deg);
}
</style>
